@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">View Schedule PINs</h1>
        <a href="{{ route('admin.schedule-pins.manage') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
            Manage PINs
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.schedule-pins.view') }}" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Schedule Code</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter schedule code..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.schedule-pins.view') }}" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <p class="mb-4 text-gray-600">
        Below are all schedules with their PINs. PINs are shown for schedules that have been assigned one.
    </p>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="table-auto w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Code</th>
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
                            {{ optional($schedule->course)->code }} 
                            <!-- - {{ optional($schedule->course)->title }} -->
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
                            No schedules found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <!-- <div class="mt-4">
        {{ $schedules->links() }}
    </div> -->
</div>
@endsection

