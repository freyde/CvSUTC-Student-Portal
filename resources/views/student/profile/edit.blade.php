@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">My Profile</h1>
        <a href="{{ route('student.portal.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">← Back to Portal</a>
    </div>

    @if(session('status') === 'password-updated')
        <div class="mb-4 rounded border border-green-200 bg-green-50 px-3 py-2 text-green-800">
            Password updated successfully.
        </div>
    @endif

    <!-- Profile Information (Read-only) -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Profile Information</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Student Number</label>
                <div class="mt-1 text-lg text-gray-900 font-medium">{{ $user->student_number ?? 'N/A' }}</div>
            </div>
            <br>
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <div class="mt-1 text-lg text-gray-900 font-medium">{{ $user->name }}</div>
            </div>
            @if($user->email)
                <br>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $user->email }}</div>
                </div>
            @endif
            @if($user->program)
                <br>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program</label>
                    <div class="mt-1 text-lg text-gray-900 font-medium">{{ $user->program->name }}</div>
                </div>
            @endif
        </div>
        <p class="mt-4 text-sm text-gray-500">Note: Profile information cannot be changed. Please contact an administrator if you need to update your information.</p>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Update Password</h2>
        <form method="POST" action="{{ route('student.profile.update-password') }}">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input 
                    type="password" 
                    name="current_password" 
                    id="current_password" 
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('current_password', 'updatePassword') border-red-500 @enderror"
                >
                @error('current_password', 'updatePassword')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password', 'updatePassword') border-red-500 @enderror"
                >
                @error('password', 'updatePassword')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

