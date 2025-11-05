@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Manage Grades</h1>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.grades.index') }}" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Student Number</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student number..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.grades.index') }}" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grades</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($enrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $enrollment->user->student_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->course->code }} - {{ $enrollment->course->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule ? $enrollment->schedule->schedule_code : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="space-y-2">
                                @foreach($enrollment->grades as $grade)
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{ $grade->item }}:</span>
                                        <span>{{ $grade->score ?? 'N/A' }}</span>
                                        <form action="{{ route('admin.grades.delete-grade', $grade) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs" onclick="return confirm('Delete this grade?')">×</button>
                                        </form>
                                    </div>
                                @endforeach
                                @if($enrollment->grades->isEmpty())
                                    <span class="text-gray-400">No grades</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('admin.grades.update-grade', $enrollment) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="item" placeholder="Grade Item" value="Final" required class="text-xs px-2 py-1 rounded border border-gray-300 w-20">
                                <input type="number" step="0.01" min="0" max="100" name="score" placeholder="Score" class="text-xs px-2 py-1 rounded border border-gray-300 w-20">
                                <button type="submit" class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No enrollments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>
</div>
@endsection

