@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-0">
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6">
        <h1 class="text-2xl font-semibold">My Class Schedule</h1>
    </div>

    @if(!$activeYear || !$currentSemester)
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No active semester is configured yet.</p>
        </div>
    @elseif($enrollments->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">
                You have no schedules for {{ $activeYear->year }} - {{ $currentSemester->name }}.
            </p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $activeYear->year }} - {{ $currentSemester->name }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Schedule Code
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course Code
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course Title
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Class Schedule (Day / Time / Room)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                            @php
                                $schedule = $enrollment->schedule;
                            @endphp
                            <tr>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                    {{ $schedule->schedule_code }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                    {{ $schedule->course->code }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-xs sm:text-sm text-gray-500">
                                    {{ $schedule->course->title }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                    {{ $schedule->instructor?->name ?? 'TBA' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-xs sm:text-sm text-gray-500">
                                    @if($schedule->meetings->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <div class="space-y-1">
                                            @foreach($schedule->meetings as $meeting)
                                                <div>
                                                    {{ $meeting->day_of_week }}
                                                    {{ \Illuminate\Support\Str::substr($meeting->start_time, 0, 5) }}
                                                    -
                                                    {{ \Illuminate\Support\Str::substr($meeting->end_time, 0, 5) }}
                                                    @if($meeting->room)
                                                        ({{ $meeting->room }})
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

