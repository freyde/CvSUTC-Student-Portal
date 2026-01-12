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

    @if($schedule->finalized_at)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-green-700">
                Grades for this schedule were finalized on {{ $schedule->finalized_at->format('M d, Y H:i') }}.
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('grades.finalize', $schedule) }}" class="space-y-6" id="finalize-form">
            @csrf
            
            <div class="overflow-x-auto bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="w-full divide-y divide-gray-300 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Student</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Student No.</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                            @php
                                $grade = $enrollment->grades->firstWhere('item', 'Final');
                                $currentGrade = old("grades.{$enrollment->id}", optional($grade)->score);
                            @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $enrollment->user->name }}</td>
                                <td class="px-4 py-2 text-center">{{ $enrollment->user->student_number }}</td>
                                <td class="px-4 py-2 text-center">
                                    <select
                                        name="grades[{{ $enrollment->id }}]"
                                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">----</option>
                                        <option value="1.00" {{ $currentGrade == '1.00' ? 'selected' : '' }}>1.00</option>
                                        <option value="1.25" {{ $currentGrade == '1.25' ? 'selected' : '' }}>1.25</option>
                                        <option value="1.50" {{ $currentGrade == '1.50' ? 'selected' : '' }}>1.50</option>
                                        <option value="1.75" {{ $currentGrade == '1.75' ? 'selected' : '' }}>1.75</option>
                                        <option value="2.00" {{ $currentGrade == '2.00' ? 'selected' : '' }}>2.00</option>
                                        <option value="2.25" {{ $currentGrade == '2.25' ? 'selected' : '' }}>2.25</option>
                                        <option value="2.50" {{ $currentGrade == '2.50' ? 'selected' : '' }}>2.50</option>
                                        <option value="2.75" {{ $currentGrade == '2.75' ? 'selected' : '' }}>2.75</option>
                                        <option value="3.00" {{ $currentGrade == '3.00' ? 'selected' : '' }}>3.00</option>
                                        <option value="4.00" {{ $currentGrade == '4.00' ? 'selected' : '' }}>4.00</option>
                                        <option value="INC" {{ $currentGrade == 'INC' ? 'selected' : '' }}>INC</option>
                                        <option value="DRP" {{ $currentGrade == 'DRP' ? 'selected' : '' }}>DRP</option>
                                        <option value="5.00" {{ $currentGrade == '5.00' ? 'selected' : '' }}>5.00</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-2">Finalize Grades</h2>
                <p class="text-xs text-gray-600 mb-4">
                    Enter grades for all students using the dropdown menus above. Once finalized, grades for this schedule will be locked. Enter the <strong>schedule approval PIN</strong>
                    provided by your department chair to confirm.
                </p>
                <div class="max-w-xs mb-4">
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
                        Save & Finalize Grades
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

