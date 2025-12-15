<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        
        return view('auth.register', compact('programs'));
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
            'role' => ['required', 'string', 'in:student,teacher,admin'],
        ];

        // Email is required for teachers/admins, optional for students
        if ($request->role !== 'student') {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
        } else {
            $rules['email'] = ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
            $rules['student_number'] = ['required', 'string', 'max:255', 'unique:users,student_number'];
            $rules['program_id'] = ['nullable', 'exists:programs,id'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_number' => $validated['student_number'] ?? null,
            'program_id' => $validated['program_id'] ?? null,
        ]);

        event(new Registered($user));

        return redirect()->route('register')->with('status', 'User registered successfully.');
    }
}
