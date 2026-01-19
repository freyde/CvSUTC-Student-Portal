<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        if ($role === 'chair') {
            // Show users who are department chairs (check if they exist as chair_id in departments table)
            $query->whereIn('id', DB::table('departments')->select('chair_id')->whereNotNull('chair_id'));
        } elseif ($role === 'teacher') {
            // Show teachers who are NOT department chairs
            $query->where('role', 'teacher')
                  ->whereNotIn('id', DB::table('departments')->select('chair_id')->whereNotNull('chair_id'));
        } elseif (in_array($role, ['student', 'admin'])) {
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

    /**
     * Generate a password with 8 characters, excluding confusing characters
     * Excludes: O, 0, I, l, 1 (to avoid confusion between O/0, I/l/1)
     */
    private function generateSecurePassword($length = 8)
    {
        // Character set excluding confusing characters: O, 0, I, l, 1
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $password = '';
        $max = strlen($characters) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $max)];
        }
        
        return $password;
    }

    public function generatePassword(Request $request, User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $newPassword = $this->generateSecurePassword(8);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('generated_password', [
            'user_id' => $user->id,
            'user' => $user->name,
            'value' => $newPassword,
        ])->with('status', "Generated a new temporary password for {$user->name}.");
    }

    public function viewPassword(Request $request, User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $request->validate([
            'admin_password' => ['required', 'current_password'],
        ]);

        // Passwords are hashed; generate a fresh temporary password to display
        $newPassword = $this->generateSecurePassword(8);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('generated_password', [
            'user_id' => $user->id,
            'user' => $user->name,
            'value' => $newPassword,
        ])->with('status', "Generated a new temporary password for {$user->name}.");
    }

    public function edit(User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $programs = Program::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        
        // Check if user is a department chair
        $isDepartmentChair = $user->isDepartmentChair();
        $chairedDepartment = $user->chairedDepartment()->first();

        return view('admin.users.edit', compact('user', 'programs', 'departments', 'isDepartmentChair', 'chairedDepartment'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:student,teacher,admin,department_chair'],
        ];

        // Email validation - required for non-students, optional for students
        if ($request->role !== 'student') {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id];
        } else {
            $rules['email'] = ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id];
            $rules['student_number'] = ['required', 'string', 'max:255', 'unique:users,student_number,' . $user->id];
            $rules['program_id'] = ['nullable', 'exists:programs,id'];
        }

        // Department is required for department_chair role
        if ($request->role === 'department_chair') {
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        // Store the old department chair status
        $oldChairedDepartment = $user->chairedDepartment()->first();
        
        // Department chairs are stored as teachers
        $role = $validated['role'] === 'department_chair' ? 'teacher' : $validated['role'];

        // Prepare update data
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'role' => $role,
        ];

        // Add student-specific fields
        if ($role === 'student') {
            $updateData['student_number'] = $validated['student_number'] ?? null;
            $updateData['program_id'] = $validated['program_id'] ?? null;
            $updateData['email'] = $validated['email'] ?? null;
        } else {
            // Clear student-specific fields for non-students
            $updateData['student_number'] = null;
            $updateData['program_id'] = null;
        }

        // Update the user
        $user->update($updateData);

        // Handle department chair assignment
        if ($validated['role'] === 'department_chair' && isset($validated['department_id'])) {
            // Remove old chair assignment if different
            if ($oldChairedDepartment && $oldChairedDepartment->id != $validated['department_id']) {
                $oldChairedDepartment->update(['chair_id' => null]);
            }

            // Assign new chair
            $department = Department::find($validated['department_id']);
            if ($department && $department->chair_id != $user->id) {
                // Remove previous chair if exists
                if ($department->chair_id) {
                    $previousChair = User::find($department->chair_id);
                    if ($previousChair) {
                        // Don't remove if it's the same user
                        if ($previousChair->id != $user->id) {
                            $department->update(['chair_id' => null]);
                        }
                    }
                }
                $department->update(['chair_id' => $user->id]);
            }
        } else {
            // If role changed from department_chair, remove chair assignment
            if ($oldChairedDepartment) {
                $oldChairedDepartment->update(['chair_id' => null]);
            }
        }

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        if ($user->id === Auth::id()) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
            return back()->with('status', 'User deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('status', 'Unable to delete user: '.$e->getMessage());
        }
    }
}


