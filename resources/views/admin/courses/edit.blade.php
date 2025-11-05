@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Edit Course</h1>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="code" :value="__('Course Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $course->code)" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="title" :value="__('Course Title')" />
            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $course->title)" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="lec_unit" :value="__('Lec Unit')" />
                <x-text-input id="lec_unit" class="block mt-1 w-full" type="number" name="lec_unit" :value="old('lec_unit', $course->lec_unit)" required min="0" />
                <x-input-error :messages="$errors->get('lec_unit')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="lab_unit" :value="__('Lab Unit')" />
                <x-text-input id="lab_unit" class="block mt-1 w-full" type="number" name="lab_unit" :value="old('lab_unit', $course->lab_unit)" required min="0" />
                <x-input-error :messages="$errors->get('lab_unit')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>
            <x-primary-button>Update Course</x-primary-button>
        </div>
    </form>
</div>
@endsection
