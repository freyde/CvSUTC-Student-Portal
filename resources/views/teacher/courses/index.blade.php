@extends('layouts.app')

@section('content')
<h1>My Courses</h1>
<a href="{{ route('teacher.courses.create') }}">Create Course</a>
<table border="1" cellpadding="6" cellspacing="0" style="margin-top:12px;">
    <thead>
        <tr>
            <th>Code</th>
            <th>Title</th>
            <th>Enrollments</th>
        </tr>
    </thead>
    <tbody>
        @forelse($courses as $course)
            <tr>
                <td>{{ $course->code }}</td>
                <td>{{ $course->title }}</td>
                <td><a href="{{ route('teacher.grades.enrollments', $course) }}">Manage</a></td>
            </tr>
        @empty
            <tr><td colspan="3">No courses yet.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection


