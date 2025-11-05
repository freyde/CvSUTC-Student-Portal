@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Create Schedule</h1>

    <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <div>
            <x-input-label for="schedule_code" :value="__('Schedule Code')" />
            <x-text-input id="schedule_code" class="block mt-1 w-full" type="text" name="schedule_code" :value="old('schedule_code')" required autofocus />
            <x-input-error :messages="$errors->get('schedule_code')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="course_id" :value="__('Course')" />
                <select id="course_id" name="course_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="program_id" :value="__('Program (Optional)')" />
                <select id="program_id" name="program_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->code }} - {{ $program->name }}</option>
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
                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="semester_id" :value="__('Semester')" />
                <select id="semester_id" name="semester_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>{{ $semester->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('semester_id')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="year" :value="__('Year (Optional)')" />
                <x-text-input id="year" class="block mt-1 w-full" type="text" name="year" :value="old('year')" placeholder="e.g., 1st Year" />
                <x-input-error :messages="$errors->get('year')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="section" :value="__('Section (Optional)')" />
                <x-text-input id="section" class="block mt-1 w-full" type="text" name="section" :value="old('section')" placeholder="e.g., A, B, C" />
                <x-input-error :messages="$errors->get('section')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="instructor_id" :value="__('Instructor (Optional)')" />
                <select id="instructor_id" name="instructor_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('instructor_id')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>
            <x-primary-button>Create Schedule</x-primary-button>
        </div>
    </form>
</div>
@endsection

