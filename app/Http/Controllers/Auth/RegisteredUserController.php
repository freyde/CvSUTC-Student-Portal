<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        // Clear any intended redirect URL to prevent redirects
        session()->forget('url.intended');
        
        $programs = \App\Models\Program::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();
        
        return view('auth.register', compact('programs', 'departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,teacher,admin,department_chair'],
        ];

        // Email is required for teachers/admins/department_chairs, optional for students
        if ($request->role !== 'student') {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
        } else {
            $rules['email'] = ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
            $rules['student_number'] = ['required', 'string', 'max:255', 'unique:users,student_number'];
            $rules['program_id'] = ['nullable', 'exists:programs,id'];
        }

        // Department is required for department_chair role
        if ($request->role === 'department_chair') {
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        // Department chairs are stored as teachers
        $role = $validated['role'] === 'department_chair' ? 'teacher' : $validated['role'];

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'student_number' => $validated['student_number'] ?? null,
            'program_id' => $validated['program_id'] ?? null,
        ]);

        // If registering as department chair, assign them as chair of the selected department
        if ($validated['role'] === 'department_chair' && isset($validated['department_id'])) {
            $department = Department::find($validated['department_id']);
            if ($department) {
                $department->update(['chair_id' => $user->id]);
            }
        }

        event(new Registered($user));

        return redirect()->route('register')->with('status', 'User registered successfully.');
    }
}
