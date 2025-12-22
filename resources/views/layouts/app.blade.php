<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Student Portal' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav class="flex items-center gap-4 px-4 py-3 border-b bg-white">
        <a href="/" class="font-semibold">Home</a>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.programs.index') }}">Programs</a>
                <a href="{{ route('admin.courses.index') }}">Courses</a>
                <a href="{{ route('admin.academic-years.index') }}">Academic Years</a>
                <a href="{{ route('admin.semesters.index') }}">Semesters</a>
                <a href="{{ route('admin.departments.index') }}">Departments</a>
                <a href="{{ route('admin.schedules.index') }}">Schedules</a>
                <a href="{{ route('admin.enrollments.index') }}">Enrollments</a>
                <a href="{{ route('admin.grades.index') }}">Grades</a>
            @endif
            @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                <a href="{{ route('grades.select-schedule') }}">Teacher Grades</a>
                @if(optional(auth()->user()->department)->chair_id === auth()->id())
                    <a href="{{ route('teacher.chair.view-pins') }}">View Schedule PINs</a>
                    <a href="{{ route('teacher.chair.schedule-pins') }}">Manage PINs</a>
                @endif
            @endif
            @if(auth()->user()->isStudent())
                <a href="{{ route('student.portal.index') }}">My Grades</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" class="ml-auto hidden sm:block">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black">Logout</button>
            </form>
            <a href="{{ route('logout.get') }}" class="sm:hidden ml-auto px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black">Logout</a>
        @else
            <a href="{{ route('grades.select-schedule') }}" class="ml-auto px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black">Enter Grades</a>
            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black">Login</a>
        @endauth
    </nav>
    <main class="p-6 max-w-5xl mx-auto">
        @if(session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-3 py-2 text-green-800">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    @yield('scripts')
  </body>
 </html>


