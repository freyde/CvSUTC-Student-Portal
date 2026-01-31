@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">View Schedule PINs - {{ $department->name }}</h1>
        <!-- <a href="{{ route('teacher.chair.schedule-pins') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
            Manage PINs
        </a> -->
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('teacher.chair.view-pins') }}" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Schedule Code</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Enter schedule code..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">Search</button>
                @if(request('search'))
                    <a href="{{ route('teacher.chair.view-pins') }}" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    @if(!request('search'))
        <p class="mb-4 text-gray-600">
            Enter a <strong>schedule code</strong> above to view its approval PIN.
        </p>
    @else
        <p class="mb-4 text-gray-600">
            Showing results for schedule code matching: <strong>{{ request('search') }}</strong>
        </p>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year/Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval PIN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schedules as $schedule)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $schedule->schedule_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ optional($schedule->course)->code }} - {{ optional($schedule->course)->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($schedule->program)->code ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($schedule->instructor)->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $schedule->year ?? '—' }} / {{ $schedule->section ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($schedule->approval_pin)
                                <span class="font-mono font-semibold text-indigo-600">{{ $schedule->approval_pin }}</span>
                            @else
                                <span class="text-gray-400 italic">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($schedule->finalized_at)
                                <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Finalized</span>
                            @else
                                <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            @if(request('search'))
                                No schedules found for the searched schedule code.
                            @else
                                Search a schedule code to display results.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

