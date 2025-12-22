@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Manage Schedule PINs</h1>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <p class="text-sm text-gray-600">
            As a <strong>department chair</strong>, you can assign or update a
            <strong>PIN per schedule code</strong>. Teachers will use this PIN to
            finalize grades for that schedule.
        </p>

        @if (session('status'))
            <div class="mb-2 p-3 bg-green-100 text-green-800 rounded text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-2 p-3 bg-red-100 text-red-800 rounded text-sm">
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.chair.schedule-pins.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Code</label>
                <input
                    type="text"
                    name="schedule_code"
                    id="schedule-code-input"
                    value="{{ old('schedule_code') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                @error('schedule_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="schedule-preview" class="text-xs text-gray-600 bg-gray-50 border border-dashed border-gray-300 rounded p-3 hidden">
                <div><span class="font-semibold">Course:</span> <span id="preview-course"></span></div>
                <div><span class="font-semibold">Program:</span> <span id="preview-program"></span></div>
                <div><span class="font-semibold">Year/Section:</span> <span id="preview-year-section"></span></div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Approval PIN for this schedule</label>
                <input
                    type="text"
                    name="approval_pin"
                    value="{{ old('approval_pin') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                @error('approval_pin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    Share this PIN only with teachers who should be able to finalize grades for this schedule.
                </p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black text-sm font-semibold">
                    Save PIN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const input = document.getElementById('schedule-code-input');
    const previewBox = document.getElementById('schedule-preview');
    const previewCourse = document.getElementById('preview-course');
    const previewProgram = document.getElementById('preview-program');
    const previewYearSection = document.getElementById('preview-year-section');

    let debounceTimer = null;

    function clearPreview() {
        previewCourse.textContent = '';
        previewProgram.textContent = '';
        previewYearSection.textContent = '';
        previewBox.classList.add('hidden');
    }

    input.addEventListener('input', function () {
        const code = this.value.trim();

        if (!code) {
            clearPreview();
            return;
        }

        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('teacher.chair.schedule-info') }}?code=${encodeURIComponent(code)}`)
                .then(res => res.ok ? res.json() : { found: false })
                .then(data => {
                    if (!data.found) {
                        clearPreview();
                        return;
                    }

                    previewCourse.textContent = `${data.course_code || ''} - ${data.course_title || ''}`.trim();
                    previewProgram.textContent = data.program || '—';
                    previewYearSection.textContent = `${data.year || ''} ${data.section || ''}`.trim() || '—';
                    previewBox.classList.remove('hidden');
                })
                .catch(() => {
                    clearPreview();
                });
        }, 300);
    });
</script>
@endsection


