@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Manage Courses</h1>
        <div class="flex gap-2">
            <button onclick="document.getElementById('csv-upload-modal').classList.remove('hidden')" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                Import CSV
            </button>
            <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
                Create Course
            </a>
        </div>
    </div>


    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lec Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($courses as $course)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $course->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $course->lec_unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $course->lab_unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No courses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- CSV Upload Modal -->
    <div id="csv-upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Import Courses from CSV</h2>
            <p class="text-sm text-gray-600 mb-4">
                <strong>CSV format:</strong> code, title, lec_unit, lab_unit<br>
                <strong>Example:</strong> CS101, Introduction to Programming, 3, 1
            </p>
            <form action="{{ route('admin.courses.import-csv') }}" method="POST" enctype="multipart/form-data">
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
