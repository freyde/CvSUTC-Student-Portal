@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-0">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">← Back to Users</a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address (Required for teachers/admins, optional for students) -->
        <div id="email_field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600" id="email_help">Required for teachers and admins</p>
        </div>

        <!-- Role -->
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select Role</option>
                <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="department_chair" {{ old('role', $isDepartmentChair ? 'department_chair' : $user->role) == 'department_chair' ? 'selected' : '' }}>Department Chair</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Student Number (shown only for student role) -->
        <div id="student_number_field" style="display: none;">
            <x-input-label for="student_number" :value="__('Student Number')" />
            <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number" :value="old('student_number', $user->student_number)" autocomplete="off" />
            <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">Required for students</p>
        </div>

        <!-- Program (shown only for student role) -->
        <div id="program_field" style="display: none;">
            <x-input-label for="program_id" :value="__('Program')" />
            <select id="program_id" name="program_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select Program (Optional)</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ old('program_id', $user->program_id) == $program->id ? 'selected' : '' }}>
                        {{ $program->name }} ({{ $program->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
            <p class="mt-1 text-gray-600">Optional for students</p>
        </div>

        <!-- Department (shown only for department_chair role) -->
        <div id="department_field" style="display: none;">
            <x-input-label for="department_id" :value="__('Department')" />
            <select id="department_id" name="department_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $chairedDepartment?->id) == $department->id ? 'selected' : '' }}>
                        {{ $department->name }} ({{ $department->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">Required for department chairs</p>
        </div>

        <div class="flex items-center justify-end gap-4 mt-6">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>
            <x-primary-button>Update User</x-primary-button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const studentNumberField = document.getElementById('student_number_field');
    const programField = document.getElementById('program_field');
    const departmentField = document.getElementById('department_field');
    const emailField = document.getElementById('email_field');
    const emailHelp = document.getElementById('email_help');
    const emailInput = document.getElementById('email');
    const studentNumberInput = document.getElementById('student_number');
    const departmentInput = document.getElementById('department_id');

    function toggleFields() {
        const role = roleSelect.value;
        
        // Student fields
        if (role === 'student') {
            studentNumberField.style.display = 'block';
            programField.style.display = 'block';
            departmentField.style.display = 'none';
            emailField.style.display = 'block';
            emailHelp.textContent = 'Optional for students';
            emailInput.removeAttribute('required');
            studentNumberInput.setAttribute('required', 'required');
            departmentInput.removeAttribute('required');
        } 
        // Department chair fields
        else if (role === 'department_chair') {
            studentNumberField.style.display = 'none';
            programField.style.display = 'none';
            departmentField.style.display = 'block';
            emailField.style.display = 'block';
            emailHelp.textContent = 'Required for department chairs';
            emailInput.setAttribute('required', 'required');
            studentNumberInput.removeAttribute('required');
            departmentInput.setAttribute('required', 'required');
        } 
        // Teacher/Admin fields
        else if (role === 'teacher' || role === 'admin') {
            studentNumberField.style.display = 'none';
            programField.style.display = 'none';
            departmentField.style.display = 'none';
            emailField.style.display = 'block';
            emailHelp.textContent = 'Required for teachers and admins';
            emailInput.setAttribute('required', 'required');
            studentNumberInput.removeAttribute('required');
            departmentInput.removeAttribute('required');
        } 
        // No role selected
        else {
            studentNumberField.style.display = 'none';
            programField.style.display = 'none';
            departmentField.style.display = 'none';
            emailField.style.display = 'block';
            emailHelp.textContent = 'Required for teachers and admins';
            emailInput.removeAttribute('required');
            studentNumberInput.removeAttribute('required');
            departmentInput.removeAttribute('required');
        }
    }

    // Initial toggle based on current role
    toggleFields();

    // Toggle on role change
    roleSelect.addEventListener('change', toggleFields);
});
</script>
@endsection

