@extends('layouts.app')

@section('content')
<h1>Hello, {{ $user->name }}</h1>
<h2>Your Courses and Grades</h2>
<table border="1" cellpadding="6" cellspacing="0" style="margin-top:12px;">
    <thead>
        <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Final Grade</th>
        </tr>
    </thead>
    <tbody>
        @forelse($enrollments as $enrollment)
            @php
                $final = $enrollment->grades->firstWhere('item', 'Final');
            @endphp
            <tr>
                <td>{{ $enrollment->course->code }}</td>
                <td>{{ $enrollment->course->title }}</td>
                <td>{{ optional($final)->score ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No enrollments found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection


