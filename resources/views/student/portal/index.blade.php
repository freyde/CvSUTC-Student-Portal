@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Student Portal</h1>
        <a href="{{ route('student.profile.edit') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">My Profile</a>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">Welcome, {{ $user->name }}</h2>
        <p class="text-gray-600">Student Number: {{ $user->student_number ?? 'N/A' }}</p>
        @if($user->program)
            <p class="text-gray-600">Program: {{ $user->program->name }}</p>
        @endif
    </div>

    @if($groupedEnrollments->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No grades available yet.</p>
        </div>
    @else
        @foreach($groupedEnrollments as $semesterKey => $enrollments)
            <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $semesterKey }}</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                            @php
                                $finalGrade = $enrollment->grades->firstWhere('item', 'Final') 
                                    ?? $enrollment->grades->firstWhere('item', 'final')
                                    ?? $enrollment->grades->first();
                                $totalUnits = ($enrollment->schedule->course->lec_unit ?? $enrollment->course->lec_unit ?? 0) + 
                                             ($enrollment->schedule->course->lab_unit ?? $enrollment->course->lab_unit ?? 0);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $enrollment->schedule->course->code ?? $enrollment->course->code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $enrollment->schedule->course->title ?? $enrollment->course->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $finalGrade->score ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $totalUnits }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>
@endsection
