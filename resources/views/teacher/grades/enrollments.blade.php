@extends('layouts.app')

@section('content')
<h1>Enrollments for {{ $course->code }} - {{ $course->title }}</h1>

<p><em>For demo: manually create enrollments via database or seeder. Upload form saves/updates a grade per student.</em></p>

<table border="1" cellpadding="6" cellspacing="0" style="margin-top:12px;">
    <thead>
        <tr>
            <th>Student</th>
            <th>Student No.</th>
            <th>Grade Item</th>
            <th>Score</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($enrollments as $enrollment)
        @php
            $grade = $enrollment->grades->firstWhere('item', 'Final');
        @endphp
        <tr>
            <td>{{ $enrollment->user->name }}</td>
            <td>{{ $enrollment->user->student_number }}</td>
            <td>Final</td>
            <td>
                <form method="POST" action="{{ route('teacher.grades.upsert', [$course, $enrollment]) }}" style="display:flex;gap:6px;align-items:center;">
                    @csrf
                    <input type="hidden" name="item" value="Final">
                    <input type="number" step="0.01" min="0" max="100" name="score" value="{{ old('score', optional($grade)->score) }}" style="width:100px;">
                    <button type="submit">Save</button>
                </form>
            </td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection


