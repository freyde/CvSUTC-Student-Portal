@extends('layouts.app')

@section('content')
<h1>Create Course</h1>
<form method="POST" action="{{ route('teacher.courses.store') }}" style="display:flex;flex-direction:column;gap:8px;max-width:480px;">
    @csrf
    <label>Code
        <input name="code" value="{{ old('code') }}" required>
    </label>
    @error('code')
        <div style="color:#b00020;">{{ $message }}</div>
    @enderror

    <label>Title
        <input name="title" value="{{ old('title') }}" required>
    </label>
    @error('title')
        <div style="color:#b00020;">{{ $message }}</div>
    @enderror

    <button type="submit">Create</button>
</form>
@endsection


