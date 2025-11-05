@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Manage Academic Years</h1>
        <button onclick="document.getElementById('create-year-modal').classList.remove('hidden')" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
            Add Academic Year
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($academicYears as $academicYear)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $academicYear->year }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($academicYear->is_active)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="openEditModal({{ $academicYear->id }}, '{{ $academicYear->year }}', {{ $academicYear->is_active ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <form action="{{ route('admin.academic-years.destroy', $academicYear) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No academic years found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    <div id="create-year-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Add Academic Year</h2>
            <form action="{{ route('admin.academic-years.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <x-input-label for="year" :value="__('Year (e.g., 2024-2025)')" />
                    <x-text-input id="year" class="block mt-1 w-full" type="text" name="year" required autofocus />
                    <x-input-error :messages="$errors->get('year')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Set as active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('create-year-modal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                    <x-primary-button>Create</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-year-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Edit Academic Year</h2>
            <form id="edit-year-form" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <x-input-label for="edit_year" :value="__('Year (e.g., 2024-2025)')" />
                    <x-text-input id="edit_year" class="block mt-1 w-full" type="text" name="year" required />
                    <x-input-error :messages="$errors->get('year')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" id="edit_is_active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Set as active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('edit-year-modal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                    <x-primary-button>Update</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openEditModal(id, year, isActive) {
        document.getElementById('edit-year-form').action = `/admin/academic-years/${id}`;
        document.getElementById('edit_year').value = year;
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('edit-year-modal').classList.remove('hidden');
    }
</script>
@endsection

