<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Student Portal' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav x-data="{ open: false }" class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Home -->
                <div class="flex items-center">
                    <a href="/" class="font-semibold text-gray-900">Home</a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex lg:items-center lg:space-x-4 lg:flex-1 lg:justify-center lg:ml-8">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('register') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Register</a>
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Users</a>
                            <a href="{{ route('admin.programs.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Programs</a>
                            <a href="{{ route('admin.courses.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Courses</a>
                            <a href="{{ route('admin.academic-years.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Academic Years</a>
                            <a href="{{ route('admin.semesters.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Semesters</a>
                            <a href="{{ route('admin.departments.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Departments</a>
                            <a href="{{ route('admin.schedules.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Schedules</a>
                            <a href="{{ route('admin.enrollments.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Enrollments</a>
                            <a href="{{ route('admin.grades.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Grades</a>
                            <a href="{{ route('admin.schedule-pins.view') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">View PINs</a>
                            <a href="{{ route('admin.schedule-pins.manage') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Manage PINs</a>
                        @endif
                        @if(auth()->user()->isDepartmentChair())
                            <a href="{{ route('teacher.chair.dashboard') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                            <a href="{{ route('grades.select-schedule') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Teacher Grades</a>
                            <a href="{{ route('teacher.chair.view-pins') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">View Schedule PINs</a>
                        @elseif(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.dashboard') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                            <a href="{{ route('grades.select-schedule') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Teacher Grades</a>
                            <a href="{{ route('teacher.courses.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">My Courses</a>
                        @endif
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('student.portal.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">My Grades</a>
                        @endif
                    @else
                        <a href="{{ route('grades.select-schedule') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900">Enter Grades</a>
                    @endauth
                </div>

                <!-- Right side: Logout/Login -->
                <div class="flex items-center space-x-4">
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="hidden lg:block">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black text-sm">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hidden lg:block px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black text-sm">Login</a>
                    @endauth

                    <!-- Mobile menu button -->
                    <button @click="open = !open" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-900">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" x-cloak class="lg:hidden border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Register</a>
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Users</a>
                        <a href="{{ route('admin.programs.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Programs</a>
                        <a href="{{ route('admin.courses.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Courses</a>
                        <a href="{{ route('admin.academic-years.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Academic Years</a>
                        <a href="{{ route('admin.semesters.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Semesters</a>
                        <a href="{{ route('admin.departments.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Departments</a>
                        <a href="{{ route('admin.schedules.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Schedules</a>
                        <a href="{{ route('admin.enrollments.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Enrollments</a>
                        <a href="{{ route('admin.grades.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Grades</a>
                        <a href="{{ route('admin.schedule-pins.view') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">View PINs</a>
                        <a href="{{ route('admin.schedule-pins.manage') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Manage PINs</a>
                    @endif
                    @if(auth()->user()->isDepartmentChair())
                        <a href="{{ route('teacher.chair.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Dashboard</a>
                        <a href="{{ route('grades.select-schedule') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Teacher Grades</a>
                        <a href="{{ route('teacher.chair.view-pins') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">View Schedule PINs</a>
                    @elseif(auth()->user()->isTeacher())
                        <a href="{{ route('teacher.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Dashboard</a>
                        <a href="{{ route('grades.select-schedule') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Teacher Grades</a>
                        <a href="{{ route('teacher.courses.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">My Courses</a>
                    @endif
                    @if(auth()->user()->isStudent())
                        <a href="{{ route('student.portal.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">My Grades</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Logout</button>
                    </form>
                @else
                    <a href="{{ route('grades.select-schedule') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Enter Grades</a>
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Login</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="p-6 max-w-7xl mx-auto">
        @if(session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-3 py-2 text-green-800">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    @yield('scripts')
  </body>
 </html>


