@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Enter Schedule Code</h1>

    <form method="POST" action="{{ route('grades.show-schedule') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Code</label>
            <input
                type="text"
                name="schedule_code"
                id="teacher-schedule-code-input"
                value="{{ old('schedule_code') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
            @error('schedule_code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div id="teacher-schedule-preview" class="text-xs text-gray-600 bg-gray-50 border border-dashed border-gray-300 rounded p-3 hidden">
            <div><span class="font-semibold">Course:</span> <span id="teacher-preview-course"></span></div>
            <div><span class="font-semibold">Program:</span> <span id="teacher-preview-program"></span></div>
            <div><span class="font-semibold">Year/Section:</span> <span id="teacher-preview-year-section"></span></div>
        </div>

        <p class="text-xs text-gray-500">
            Paste the <strong>schedule code</strong> of the class you want to encode grades for.
        </p>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black text-sm font-semibold">
                Continue
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const tInput = document.getElementById('teacher-schedule-code-input');
    const tPreviewBox = document.getElementById('teacher-schedule-preview');
    const tPreviewCourse = document.getElementById('teacher-preview-course');
    const tPreviewProgram = document.getElementById('teacher-preview-program');
    const tPreviewYearSection = document.getElementById('teacher-preview-year-section');

    let tDebounceTimer = null;

    function clearTeacherPreview() {
        tPreviewCourse.textContent = '';
        tPreviewProgram.textContent = '';
        tPreviewYearSection.textContent = '';
        tPreviewBox.classList.add('hidden');
    }

    tInput.addEventListener('input', function () {
        const code = this.value.trim();

        if (!code) {
            clearTeacherPreview();
            return;
        }

        if (tDebounceTimer) {
            clearTimeout(tDebounceTimer);
        }

        tDebounceTimer = setTimeout(() => {
            fetch(`{{ route('grades.schedule-info') }}?code=${encodeURIComponent(code)}`)
                .then(res => res.ok ? res.json() : { found: false })
                .then(data => {
                    if (!data.found) {
                        clearTeacherPreview();
                        return;
                    }

                    tPreviewCourse.textContent = `${data.course_code || ''} - ${data.course_title || ''}`.trim();
                    tPreviewProgram.textContent = data.program || '—';
                    tPreviewYearSection.textContent = `${data.year || ''} ${data.section || ''}`.trim() || '—';
                    tPreviewBox.classList.remove('hidden');
                })
                .catch(() => {
                    clearTeacherPreview();
                });
        }, 300);
    });
</script>
@endsection
