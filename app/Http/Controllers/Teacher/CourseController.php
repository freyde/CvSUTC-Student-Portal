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
        $courses = Course::where('teacher_id', Auth::id())->latest()->get();
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
        ]);
        $data['teacher_id'] = Auth::id();
        Course::create($data);
        return redirect()->route('teacher.courses.index')->with('status', 'Course created.');
    }

    private function authorizeTeacher(): void
    {
        abort_unless(Auth::check() && Auth::user()->isTeacher(), 403);
    }
}


