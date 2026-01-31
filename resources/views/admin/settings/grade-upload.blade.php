@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Grade Upload Settings</h1>
        <p class="text-gray-600 mt-1">Control whether teachers and department chairs can upload grades.</p>
    </div>

    <div class="bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg p-6">
        <form method="POST" action="{{ route('admin.settings.grade-upload.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Hidden input to ensure value is sent when checkbox is unchecked -->
                <input type="hidden" name="allow_teacher_grade_upload" value="0">
                
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="allow_teacher_grade_upload" 
                            name="allow_teacher_grade_upload" 
                            type="checkbox" 
                            value="1"
                            {{ $allowTeacherGradeUpload ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="allow_teacher_grade_upload" class="font-medium text-gray-700">
                            Allow Teachers and Department Chairs to Upload Grades
                        </label>
                        <p class="text-gray-500 mt-1">
                            When enabled, teachers and department chairs can upload and finalize grades for their assigned schedules. 
                            When disabled, only administrators can upload grades.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black text-sm whitespace-nowrap">
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-900 mb-2">Current Status</h3>
        <p class="text-sm text-blue-800">
            @if($allowTeacherGradeUpload)
                <span class="font-medium text-green-700">✓ Enabled:</span> Teachers and department chairs can currently upload grades.
            @else
                <span class="font-medium text-red-700">✗ Disabled:</span> Only administrators can upload grades.
            @endif
        </p>
    </div>
</div>
@endsection

