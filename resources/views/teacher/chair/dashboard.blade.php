@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Department Chair Dashboard</h1>
        @if($department)
            <p class="text-gray-600 mt-1">Welcome, {{ Auth::user()->name }} - {{ $department->name }} ({{ $department->code }})</p>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Manage Schedule PINs Card -->
        <!-- <div class="bg-white rounded-lg shadow ring-1 ring-black ring-opacity-5 p-6">
            <h2 class="text-lg font-semibold mb-2">Manage Schedule PINs</h2>
            <p class="text-gray-600 text-sm mb-4">Assign or update approval PINs for schedule codes.</p>
            <a href="{{ route('teacher.chair.schedule-pins') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                Manage PINs
            </a>
        </div> -->

        <!-- View Schedule PINs Card -->
        <div class="bg-white rounded-lg shadow ring-1 ring-black ring-opacity-5 p-6">
            <h2 class="text-lg font-semibold mb-2">View Schedule PINs</h2>
            <p class="text-gray-600 text-sm mb-4">View all schedules in your department with their PINs.</p>
            <a href="{{ route('teacher.chair.view-pins') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                View PINs
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

        <!-- Teacher Grades Card -->
        <div class="bg-white rounded-lg shadow ring-1 ring-black ring-opacity-5 p-6">
            <h2 class="text-lg font-semibold mb-2">Teacher Grades</h2>
            <p class="text-gray-600 text-sm mb-4">Enter and manage grades for your classes.</p>
            <a href="{{ route('grades.select-schedule') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                Enter Grades
            </a>
        </div>
    </div>
</div>
@endsection

