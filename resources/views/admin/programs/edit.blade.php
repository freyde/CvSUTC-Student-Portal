@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Edit Program</h1>

    <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="code" :value="__('Program Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $program->code)" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Program Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $program->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $program->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="majors" :value="__('Majors (Optional)')" />
            <textarea id="majors" name="majors" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="List majors separated by commas">{{ old('majors', $program->majors) }}</textarea>
            <x-input-error :messages="$errors->get('majors')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">Optional: List available majors for this program</p>
        </div>

        <div>
            <x-input-label for="courses" :value="__('Select Courses')" />
            <div class="mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-4">
                @forelse($courses as $course)
                    <label class="flex items-center py-2">
                        <input type="checkbox" name="courses[]" value="{{ $course->id }}" 
                               {{ $program->courses->contains($course->id) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $course->code }} - {{ $course->title }}</span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">No courses available. <a href="{{ route('admin.courses.create') }}" class="text-blue-600 underline">Create a course first</a>.</p>
                @endforelse
            </div>
            <x-input-error :messages="$errors->get('courses')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.programs.index') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>
            <x-primary-button>Update Program</x-primary-button>
        </div>
    </form>
</div>
@endsection

