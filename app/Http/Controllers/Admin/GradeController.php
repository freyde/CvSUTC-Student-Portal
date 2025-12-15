<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::orderBy('code')->get();

        // Only load data if both academic year and semester are selected
        $enrollments = collect();
        
        if ($request->filled('academic_year_id') && $request->filled('semester_id')) {
            $query = Enrollment::with('user', 'course', 'schedule.course', 'schedule.academicYear', 'schedule.semester', 'grades');

            // Filter by academic year and semester through schedule
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id)
                  ->where('semester_id', $request->semester_id);
            });

            // Search by student number
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('student_number', 'like', "%{$search}%");
                });
            }

            $enrollments = $query->latest()->paginate(20)->withQueryString();
        } else {
            // Return empty paginated collection if filters are not provided
            $enrollments = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                20,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('admin.grades.index', compact('enrollments', 'academicYears', 'semesters'));
    }

    public function updateGrade(Request $request, Enrollment $enrollment)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'item' => ['required', 'string', 'max:100'],
            'score' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        Grade::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'item' => $data['item'],
            ],
            [
                'score' => $data['score'] ?? null,
            ]
        );

        return back()->with('status', 'Grade updated successfully.');
    }

    public function deleteGrade(Grade $grade)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $grade->delete();

        return back()->with('status', 'Grade deleted successfully.');
    }
}

