@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Manage Semesters</h1>
        <button onclick="document.getElementById('create-semester-modal').classList.remove('hidden')" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">
            Add Semester
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($semesters as $semester)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $semester->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $semester->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="openEditModal({{ $semester->id }}, '{{ $semester->name }}', '{{ $semester->code }}')" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <form action="{{ route('admin.semesters.destroy', $semester) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No semesters found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    <div id="create-semester-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Add Semester</h2>
            <form action="{{ route('admin.semesters.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Semester Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="code" :value="__('Semester Code')" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required placeholder="e.g., 1ST, 2ND, SUMMER" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('create-semester-modal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                    <x-primary-button>Create</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-semester-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">Edit Semester</h2>
            <form id="edit-semester-form" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Semester Name')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="edit_code" :value="__('Semester Code')" />
                    <x-text-input id="edit_code" class="block mt-1 w-full" type="text" name="code" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('edit-semester-modal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                    <x-primary-button>Update</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openEditModal(id, name, code) {
        document.getElementById('edit-semester-form').action = `/admin/semesters/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_code').value = code;
        document.getElementById('edit-semester-modal').classList.remove('hidden');
    }
</script>
@endsection

