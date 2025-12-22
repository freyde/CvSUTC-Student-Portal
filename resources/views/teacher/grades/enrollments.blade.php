@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Enrollments for {{ $schedule->schedule_code }}</h1>
            <p class="text-sm text-gray-600">
                {{ optional($schedule->course)->code }} - {{ optional($schedule->course)->title }}
                @if($schedule->year || $schedule->section)
                    • {{ $schedule->year }} {{ $schedule->section }}
                @endif
            </p>
            <p class="text-xs text-gray-500">
                Academic Year: {{ optional($schedule->academicYear)->year }} • Semester: {{ optional($schedule->semester)->name }}
            </p>
        </div>
        <div class="text-right text-sm">
            @if($schedule->finalized_at)
                <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                    Finalized {{ $schedule->finalized_at->format('M d, Y H:i') }}
                </span>
            @else
                <span class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">
                    Not finalized
                </span>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="mb-2 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
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

    <div class="overflow-x-auto bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg">
        <table class="w-full divide-y divide-gray-300 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Student</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Student No.</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Grade Item</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Score</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($enrollments as $enrollment)
                    @php
                        $grade = $enrollment->grades->firstWhere('item', 'Final');
                    @endphp
                    <tr>
                        <td class="px-4 py-2">{{ $enrollment->user->name }}</td>
                        <td class="px-4 py-2">{{ $enrollment->user->student_number }}</td>
                        <td class="px-4 py-2">Final</td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('grades.upsert', [$schedule, $enrollment]) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="item" value="Final">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    name="score"
                                    value="{{ old('score', optional($grade)->score) }}"
                                    class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @if($schedule->finalized_at) disabled @endif
                                >
                                <button
                                    type="submit"
                                    class="px-3 py-1 rounded bg-gray-900 text-white hover:bg-black text-xs"
                                    @if($schedule->finalized_at) disabled @endif
                                >
                                    Save
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-2"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        @if($schedule->finalized_at)
            <p class="text-sm text-green-700">
                Grades for this schedule were finalized on {{ $schedule->finalized_at->format('M d, Y H:i') }}.
            </p>
        @else
            <form method="POST" action="{{ route('grades.finalize', $schedule) }}" class="space-y-3">
                @csrf
                <h2 class="text-sm font-semibold text-gray-800">Finalize Grades</h2>
                <p class="text-xs text-gray-600">
                    Once finalized, grades for this schedule will be locked. Enter the <strong>schedule approval PIN</strong>
                    provided by your department chair to confirm.
                </p>
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Approval PIN</label>
                    <input
                        type="password"
                        name="approval_pin"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                </div>
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black text-sm font-semibold"
                    >
                        Finalize Grades
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

