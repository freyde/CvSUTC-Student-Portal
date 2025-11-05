@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Manage Enrollments</h1>
        <div class="flex gap-2">
            <button onclick="document.getElementById('csv-upload-modal').classList.remove('hidden')" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                Import CSV
            </button>
            <button onclick="document.getElementById('create-enrollment-modal').classList.remove('hidden')" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
                Add Enrollment
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Student Number or Name</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student number or name..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Schedule Code</label>
                <input type="text" name="schedule_code" value="{{ request('schedule_code') }}" placeholder="Enter schedule code..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">Filter</button>
                @if(request('search') || request('schedule_code'))
                    <a href="{{ route('admin.enrollments.index') }}" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Clear</a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lec Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($enrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $enrollment->user->student_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->user->program ? $enrollment->user->program->code : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule ? $enrollment->schedule->schedule_code : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule && $enrollment->schedule->course ? $enrollment->schedule->course->code : ($enrollment->course ? $enrollment->course->code : 'N/A') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $enrollment->schedule && $enrollment->schedule->course ? $enrollment->schedule->course->title : ($enrollment->course ? $enrollment->course->title : 'N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule && $enrollment->schedule->course ? $enrollment->schedule->course->lec_unit : ($enrollment->course ? $enrollment->course->lec_unit : 'N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $enrollment->schedule && $enrollment->schedule->course ? $enrollment->schedule->course->lab_unit : ($enrollment->course ? $enrollment->course->lab_unit : 'N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">No enrollments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>

    <!-- Create Enrollment Modal -->
    <div id="create-enrollment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Add Enrollment</h2>
            <form action="{{ route('admin.enrollments.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <x-input-label for="student_number" :value="__('Student Number')" />
                    <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number" required autofocus />
                    <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="schedule_code" :value="__('Schedule Code')" />
                    <x-text-input id="schedule_code" class="block mt-1 w-full" type="text" name="schedule_code" required />
                    <x-input-error :messages="$errors->get('schedule_code')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('create-enrollment-modal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                    <x-primary-button>Create</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- CSV Upload Modal -->
    <div id="csv-upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Import Enrollments from CSV</h2>
            <p class="text-sm text-gray-600 mb-4">
                <strong>CSV format:</strong> student_number, schedule_code<br>
                <strong>Example:</strong> S-0001, SCH001
            </p>
            <form action="{{ route('admin.enrollments.import-csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black">
                    <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('csv-upload-modal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                    <x-primary-button>Import</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @if(session('import_errors'))
        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-red-800 mb-2">Import Errors:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1 max-h-60 overflow-y-auto">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection

