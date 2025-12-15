<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $role = $request->query('role', 'all');
        $search = $request->query('search');
        
        $query = User::with('program')->orderBy('name');
        
        // Role filter
        if (in_array($role, ['student', 'teacher', 'admin'])) {
            $query->where('role', $role);
        }
        
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }
        
        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'role', 'search'));
    }

    public function importFromCsv(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
        ]);

        $data = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));

        // Detect header
        if (!empty($data)) {
            $firstRow = array_map('strtolower', array_map('trim', $data[0]));
            if (in_array('name', $firstRow) || in_array('role', $firstRow)) {
                array_shift($data);
            }
        }

        // Expected: name, email(optional for students), role(student|teacher|admin), student_number(optional unless student), program_code(optional)
        $imported = 0;
        $errors = [];
        foreach ($data as $index => $row) {
            if (count(array_filter($row)) === 0) continue;

            if (count($row) < 3) {
                $errors[] = 'Row '.($index+2).': Insufficient columns. Expected at least: name, email(optional), role';
                continue;
            }

            $name = trim($row[0] ?? '');
            $email = trim($row[1] ?? '');
            $role = strtolower(trim($row[2] ?? ''));
            $studentNumber = trim($row[3] ?? '');
            $programCode = trim($row[4] ?? '');

            $programId = null;
            if ($programCode !== '') {
                $program = Program::where('code', $programCode)->first();
                if (!$program) {
                    $errors[] = 'Row '.($index+2).": Program code '{$programCode}' not found";
                    continue;
                }
                $programId = $program->id;
            }

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'role' => ['required', 'in:student,teacher,admin'],
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'student_number' => ['nullable', 'string', 'max:255', 'unique:users,student_number'],
            ];

            if ($role !== 'student') {
                $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'];
            } else {
                $rules['student_number'] = ['required', 'string', 'max:255', 'unique:users,student_number'];
            }

            $validator = Validator::make([
                'name' => $name,
                'email' => $email ?: null,
                'role' => $role,
                'student_number' => $studentNumber ?: null,
            ], $rules);

            if ($validator->fails()) {
                $errors[] = 'Row '.($index+2).' ('.($email ?: $studentNumber ?: 'N/A').'): '.implode(', ', $validator->errors()->all());
                continue;
            }

            try {
                User::create([
                    'name' => $name,
                    'email' => $email ?: null,
                    'role' => $role,
                    'student_number' => $studentNumber ?: null,
                    'program_id' => $programId,
                    'password' => null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = 'Row '.($index+2).': '.$e->getMessage();
            }
        }

        $message = "Imported {$imported} user(s) successfully.";
        if (!empty($errors)) {
            $message .= ' '.count($errors).' error(s) occurred.';
            session()->flash('import_errors', $errors);
        }

        return back()->with('status', $message);
    }

    public function generatePassword(User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $newPassword = Str::random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('status', "Generated password for {$user->name}: {$newPassword}");
    }

    public function destroy(User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        if ($user->id === Auth::id()) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        if ($user->role === 'admin') {
            return back()->with('status', 'You cannot delete admin accounts.');
        }

        try {
            $user->delete();
            return back()->with('status', 'User deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('status', 'Unable to delete user: '.$e->getMessage());
        }
    }
}


