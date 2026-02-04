@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Edit Schedule</h1>

    <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="schedule_code" :value="__('Schedule Code')" />
            <x-text-input id="schedule_code" class="block mt-1 w-full" type="text" name="schedule_code" :value="old('schedule_code', $schedule->schedule_code)" required autofocus />
            <x-input-error :messages="$errors->get('schedule_code')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="course_id" :value="__('Course')" />
                <select id="course_id" name="course_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $schedule->course_id) == $course->id ? 'selected' : '' }}>{{ $course->code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="program_id" :value="__('Program (Optional)')" />
                <select id="program_id" name="program_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id', $schedule->program_id) == $program->id ? 'selected' : '' }}>{{ $program->code }} - {{ $program->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="academic_year_id" :value="__('Academic Year')" />
                <select id="academic_year_id" name="academic_year_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Academic Year</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ old('academic_year_id', $schedule->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="semester_id" :value="__('Semester')" />
                <select id="semester_id" name="semester_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ old('semester_id', $schedule->semester_id) == $semester->id ? 'selected' : '' }}>{{ $semester->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('semester_id')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="year" :value="__('Year (Optional)')" />
                <x-text-input id="year" class="block mt-1 w-full" type="text" name="year" :value="old('year', $schedule->year)" placeholder="e.g., 1st Year" />
                <x-input-error :messages="$errors->get('year')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="section" :value="__('Section (Optional)')" />
                <x-text-input id="section" class="block mt-1 w-full" type="text" name="section" :value="old('section', $schedule->section)" placeholder="e.g., A, B, C" />
                <x-input-error :messages="$errors->get('section')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="instructor_id" :value="__('Instructor (Optional)')" />
                <select id="instructor_id" name="instructor_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id', $schedule->instructor_id) == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('instructor_id')" class="mt-2" />
            </div>
        </div>

        <div class="border-t pt-4 mt-4">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Class Schedule (up to 3)</h2>
            <p class="text-xs text-gray-500 mb-3">Specify day, start time, end time, and room for each meeting.</p>
            @php
                $existingMeetings = $schedule->meetings->take(3)->values();
            @endphp
            <div class="space-y-3">
                @foreach(range(0,2) as $i)
                    @php $m = $existingMeetings[$i] ?? null; @endphp
                    <div class="grid grid-cols-4 gap-3 items-end">
                        <div>
                            <x-input-label :for="'meetings_'.$i.'_day'" :value="__('Day')" />
                            <select id="meetings_{{ $i }}_day" name="meetings[{{ $i }}][day]" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">—</option>
                                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                                    <option value="{{ $day }}" {{ old('meetings.'.$i.'.day', $m?->day_of_week) === $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label :for="'meetings_'.$i.'_start_time'" :value="__('Start Time')" />
                            <x-text-input
                                id="meetings_{{ $i }}_start_time"
                                class="block mt-1 w-full"
                                type="time"
                                name="meetings[{{ $i }}][start_time]"
                                :value="old('meetings.'.$i.'.start_time', $m?->start_time)"
                            />
                        </div>
                        <div>
                            <x-input-label :for="'meetings_'.$i.'_end_time'" :value="__('End Time')" />
                            <x-text-input
                                id="meetings_{{ $i }}_end_time"
                                class="block mt-1 w-full"
                                type="time"
                                name="meetings[{{ $i }}][end_time]"
                                :value="old('meetings.'.$i.'.end_time', $m?->end_time)"
                            />
                        </div>
                        <div>
                            <x-input-label :for="'meetings_'.$i.'_room'" :value="__('Room')" />
                            <x-text-input
                                id="meetings_{{ $i }}_room"
                                class="block mt-1 w-full"
                                type="text"
                                name="meetings[{{ $i }}][room]"
                                :value="old('meetings.'.$i.'.room', $m?->room)"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>
            <x-primary-button>Update Schedule</x-primary-button>
        </div>
    </form>
</div>
@endsection

