<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function enrollments(Course $course)
    {
        $this->authorizeTeacherCourse($course);
        $enrollments = $course->enrollments()->with('user', 'grades')->get();
        return view('teacher.grades.enrollments', compact('course', 'enrollments'));
    }

    public function upsert(Request $request, Course $course, Enrollment $enrollment)
    {
        $this->authorizeTeacherCourse($course);
        abort_unless($enrollment->course_id === $course->id, 404);

        $data = $request->validate([
            'item' => ['nullable', 'string', 'max:100'],
            'score' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        Grade::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'item' => $data['item'] ?? 'Final',
            ],
            [
                'score' => $data['score'] ?? null,
            ],
        );

        return back()->with('status', 'Grade saved.');
    }

    private function authorizeTeacherCourse(Course $course): void
    {
        abort_unless(Auth::check() && Auth::user()->isTeacher() && $course->teacher_id === Auth::id(), 403);
    }
}


