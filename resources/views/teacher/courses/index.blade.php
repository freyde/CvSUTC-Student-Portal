@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">My Courses</h1>
    <div class="flex gap-2">
        @php
            $user = auth()->user();
            $isChair = $user && $user->isTeacher() && $user->department && $user->department->chair_id === $user->id;
        @endphp
        @if($isChair)
            <a href="{{ route('teacher.chair.view-pins') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
                View Schedule PINs
            </a>
            <a href="{{ route('teacher.chair.schedule-pins') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
                Manage PINs
            </a>
        @endif
        <a href="{{ route('teacher.courses.create') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
            Create Course
        </a>
    </div>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedules</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($courses as $course)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $course->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($course->schedules && $course->schedules->count() > 0)
                            {{ $course->schedules->count() }} schedule(s)
                        @else
                            No schedules
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('grades.select-schedule') }}" class="text-blue-600 hover:text-blue-900">Enter Grades</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No courses found. Courses are assigned to you through schedules.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


