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
    <nav class="bg-white border-b" x-data="{ 
        openUsers: false, 
        openAcademicYear: false, 
        openCurriculum: false, 
        openEnrollments: false, 
        openPINs: false 
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Home -->
                <div class="flex items-center">
                    <a href="/" class="font-semibold text-gray-900">Home</a>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4 flex-1 justify-center ml-8">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <!-- Users Dropdown -->
                            <div class="relative" @click.away="openUsers = false">
                                <button @click="openUsers = !openUsers" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap flex items-center">
                                    Users
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openUsers" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                                    <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Register</a>
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Users</a>
                                </div>
                            </div>

                            <!-- Academic Year Dropdown -->
                            <div class="relative" @click.away="openAcademicYear = false">
                                <button @click="openAcademicYear = !openAcademicYear" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap flex items-center">
                                    Academic Year
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openAcademicYear" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                                    <a href="{{ route('admin.academic-years.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Academic Years</a>
                                    <a href="{{ route('admin.semesters.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Semesters</a>
                                </div>
                            </div>

                            <!-- Curriculum Dropdown -->
                            <div class="relative" @click.away="openCurriculum = false">
                                <button @click="openCurriculum = !openCurriculum" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap flex items-center">
                                    Curriculum
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openCurriculum" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                                    <a href="{{ route('admin.departments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Departments</a>
                                    <a href="{{ route('admin.programs.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Programs</a>
                                    <a href="{{ route('admin.courses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Courses</a>
                                </div>
                            </div>

                            <!-- Schedules (standalone) -->
                            <a href="{{ route('admin.schedules.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">Schedules</a>

                            <!-- Enrollments Dropdown -->
                            <div class="relative" @click.away="openEnrollments = false">
                                <button @click="openEnrollments = !openEnrollments" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap flex items-center">
                                    Enrollments
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openEnrollments" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                                    <a href="{{ route('admin.enrollments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Enrollments</a>
                                    <a href="{{ route('admin.grades.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Grades</a>
                                </div>
                            </div>

                            <!-- PINs Dropdown -->
                            <div class="relative" @click.away="openPINs = false">
                                <button @click="openPINs = !openPINs" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap flex items-center">
                                    PINs
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openPINs" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                                    <a href="{{ route('admin.schedule-pins.view') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View PINs</a>
                                    <a href="{{ route('admin.schedule-pins.manage') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage PINs</a>
                                </div>
                            </div>
                        @endif
                        @if(auth()->user()->isDepartmentChair())
                            <a href="{{ route('teacher.chair.dashboard') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">Dashboard</a>
                            <a href="{{ route('grades.select-schedule') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">Teacher Grades</a>
                            <a href="{{ route('teacher.chair.view-pins') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">View Schedule PINs</a>
                        @elseif(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.dashboard') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">Dashboard</a>
                            <a href="{{ route('grades.select-schedule') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">Teacher Grades</a>
                            <a href="{{ route('teacher.courses.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">My Courses</a>
                        @endif
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('student.portal.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">My Grades</a>
                        @endif
                    @else
                        <a href="{{ route('grades.select-schedule') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 whitespace-nowrap">Enter Grades</a>
                    @endauth
                </div>

                <!-- Right side: Logout/Login -->
                <div class="flex items-center space-x-4">
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black text-sm whitespace-nowrap">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black text-sm whitespace-nowrap">Login</a>
                    @endauth
                </div>
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


