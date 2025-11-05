@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Register New User</h1>
        <button
            x-data
            @click="$dispatch('open-modal', 'import-users')"
            class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Import CSV
        </button>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address (Required for teachers/admins, optional for students) -->
        <div id="email_field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600" id="email_help">Required for teachers and admins</p>
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role -->
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select Role</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Student Number (shown only for student role) -->
        <div id="student_number_field" style="display: none;">
            <x-input-label for="student_number" :value="__('Student Number')" />
            <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number" :value="old('student_number')" autocomplete="off" />
            <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">Required for students</p>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>

@endsection

@section('modals')
<x-modal name="import-users" :show="false" focusable>
    <form method="POST" action="{{ route('admin.users.import-csv') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Import Users from CSV</h2>
        <p class="mt-1 text-sm text-gray-600">Columns: name, email(optional for students), role(student|teacher|admin), student_number(optional unless student), program_code(optional)</p>
        <div class="mt-4">
            <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button type="submit">Upload</x-primary-button>
        </div>
        <div class="mt-4 text-xs text-gray-500">
            Example:
            <pre class="bg-gray-100 p-2 rounded">name,email,role,student_number,program_code
Jane Admin,jane.admin@example.com,admin,,
Tom Teacher,tom.teacher@example.com,teacher,,
Ana Student,,student,2023-00001,BSCS</pre>
        </div>
    </form>
    @if (session('import_errors'))
        <div class="px-6 pb-6">
            <div class="mt-4 p-3 bg-red-100 text-red-800 rounded">
                <div class="font-semibold mb-2">Import errors:</div>
                <ul class="list-disc ml-6 space-y-1">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</x-modal>
@endsection

@section('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const studentNumberField = document.getElementById('student_number_field');
        const studentNumberInput = document.getElementById('student_number');
        const emailInput = document.getElementById('email');
        const emailHelp = document.getElementById('email_help');
        
        if (this.value === 'student') {
            studentNumberField.style.display = 'block';
            studentNumberInput.setAttribute('required', 'required');
            emailInput.removeAttribute('required');
            emailHelp.textContent = 'Optional for students (not used for login)';
        } else {
            studentNumberField.style.display = 'none';
            studentNumberInput.removeAttribute('required');
            emailInput.setAttribute('required', 'required');
            emailHelp.textContent = 'Required for teachers and admins';
        }
    });
    
    // Initialize on page load
    if (document.getElementById('role').value === 'student') {
        document.getElementById('student_number_field').style.display = 'block';
        document.getElementById('student_number').setAttribute('required', 'required');
        document.getElementById('email').removeAttribute('required');
        document.getElementById('email_help').textContent = 'Optional for students (not used for login)';
    } else {
        document.getElementById('email').setAttribute('required', 'required');
    }
</script>
@endsection
