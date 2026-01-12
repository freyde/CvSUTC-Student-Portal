@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Teacher Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome, {{ Auth::user()->name }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Teacher Grades Card -->
        <div class="bg-white rounded-lg shadow ring-1 ring-black ring-opacity-5 p-6">
            <h2 class="text-lg font-semibold mb-2">Enter Grades</h2>
            <p class="text-gray-600 text-sm mb-4">Enter and manage grades for your classes.</p>
            <a href="{{ route('grades.select-schedule') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                Enter Grades
            </a>
        </div>

        <!-- My Courses Card -->
        <div class="bg-white rounded-lg shadow ring-1 ring-black ring-opacity-5 p-6">
            <h2 class="text-lg font-semibold mb-2">My Courses</h2>
            <p class="text-gray-600 text-sm mb-4">View courses you are teaching.</p>
            <a href="{{ route('teacher.courses.index') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                View Courses
            </a>
        </div>
    </div>
</div>
@endsection

