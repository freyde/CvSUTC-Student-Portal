@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Manage Grades</h1>
        @if(request('academic_year_id') && request('semester_id'))
            <a href="{{ route('admin.grades.export', request()->query()) }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black text-sm">
                Export CSV
            </a>
        @endif
    </div>

    <!-- Valid Grades Reference -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-2">Valid Grade Values:</h2>
        <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">1.00</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">1.25</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">1.50</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">1.75</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">2.00</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">2.25</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">2.50</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">2.75</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">3.00</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">4.00</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">Drp.</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">Inc.</span>
            <span class="px-3 py-1 bg-white rounded border border-gray-300 text-sm">5.00</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.grades.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                    <select name="academic_year_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" {{ request('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                {{ $academicYear->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
                    <select name="semester_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search by Student Number</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student number..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">Filter</button>
                @if(request('academic_year_id') || request('semester_id') || request('search'))
                    <a href="{{ route('admin.grades.index') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    @if(!request('academic_year_id') || !request('semester_id'))
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">Please select an Academic Year and Semester to view grades.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="table-auto w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $enrollment->user->student_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule->course->code ?? $enrollment->course->code }} - {{ $enrollment->schedule->course->title ?? $enrollment->course->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule ? $enrollment->schedule->schedule_code : 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @php $finalGrade = $enrollment->grades->firstWhere('item', 'Final'); @endphp
                                <form method="POST" action="{{ route('admin.grades.update-grade', $enrollment) }}" class="inline" onchange="this.submit()">
                                    @csrf
                                    <input type="hidden" name="item" value="Final">
                                    <select name="score" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1">
                                        <option value="" {{ $finalGrade?->score === null ? 'selected' : '' }}>----</option>
                                        <option value="1.00" {{ (string) ($finalGrade?->score ?? '') === '1.00' ? 'selected' : '' }}>1.00</option>
                                        <option value="1.25" {{ (string) ($finalGrade?->score ?? '') === '1.25' ? 'selected' : '' }}>1.25</option>
                                        <option value="1.50" {{ (string) ($finalGrade?->score ?? '') === '1.50' ? 'selected' : '' }}>1.50</option>
                                        <option value="1.75" {{ (string) ($finalGrade?->score ?? '') === '1.75' ? 'selected' : '' }}>1.75</option>
                                        <option value="2.00" {{ (string) ($finalGrade?->score ?? '') === '2.00' ? 'selected' : '' }}>2.00</option>
                                        <option value="2.25" {{ (string) ($finalGrade?->score ?? '') === '2.25' ? 'selected' : '' }}>2.25</option>
                                        <option value="2.50" {{ (string) ($finalGrade?->score ?? '') === '2.50' ? 'selected' : '' }}>2.50</option>
                                        <option value="2.75" {{ (string) ($finalGrade?->score ?? '') === '2.75' ? 'selected' : '' }}>2.75</option>
                                        <option value="3.00" {{ (string) ($finalGrade?->score ?? '') === '3.00' ? 'selected' : '' }}>3.00</option>
                                        <option value="4.00" {{ (string) ($finalGrade?->score ?? '') === '4.00' ? 'selected' : '' }}>4.00</option>
                                        <option value="6.00" {{ (string) ($finalGrade?->score ?? '') === '6.00' ? 'selected' : '' }}>INC</option>
                                        <option value="7.00" {{ (string) ($finalGrade?->score ?? '') === '7.00' ? 'selected' : '' }}>DRP</option>
                                        <option value="5.00" {{ (string) ($finalGrade?->score ?? '') === '5.00' ? 'selected' : '' }}>5.00</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No enrollments found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($enrollments->hasPages())
            <div class="mt-4">
                {{ $enrollments->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

