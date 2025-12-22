<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $this->authorizeTeacher();
        // Get courses through schedules where the teacher is the instructor
        $courses = Course::whereHas('schedules', function($query) {
            $query->where('instructor_id', Auth::id());
        })->with(['schedules' => function($query) {
            $query->where('instructor_id', Auth::id());
        }])->latest()->get();
        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorizeTeacher();
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $this->authorizeTeacher();
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code'],
            'title' => ['required', 'string', 'max:255'],
            'lec_unit' => ['nullable', 'integer', 'min:0'],
            'lab_unit' => ['nullable', 'integer', 'min:0'],
        ]);
        Course::create($data);
        return redirect()->route('teacher.courses.index')->with('status', 'Course created.');
    }

    private function authorizeTeacher(): void
    {
        abort_unless(Auth::check() && Auth::user()->isTeacher(), 403);
    }
}


