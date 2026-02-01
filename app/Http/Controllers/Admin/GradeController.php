<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(Request $request): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        if (!$request->filled('academic_year_id') || !$request->filled('semester_id')) {
            return redirect()->route('admin.grades.index')
                ->with('status', 'Please select an Academic Year and Semester to export grades.');
        }

        $query = Enrollment::with('user', 'schedule.course', 'schedule.instructor', 'grades')
            ->whereHas('schedule', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id)
                    ->where('semester_id', $request->semester_id);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('schedule_id')->orderBy('user_id')->get();

        // Collect grade item columns (Prelim, Midterm, Final first, then any others)
        $gradeItemOrder = ['Prelim', 'Midterm', 'Final'];
        $allItems = collect($enrollments->pluck('grades')->flatten()->pluck('item')->unique()->filter())->toArray();
        $gradeColumns = array_values(array_unique(array_merge($gradeItemOrder, array_diff($allItems, $gradeItemOrder))));

        $filename = 'grades-export-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($enrollments, $gradeColumns) {
            $handle = fopen('php://output', 'w');

            // Header row
            $header = array_merge(['Schedule Code', 'Course Code', 'Student Number', 'Instructor'], $gradeColumns);
            fputcsv($handle, $header);

            foreach ($enrollments as $enrollment) {
                $gradesByItem = $enrollment->grades->keyBy('item');

                $scheduleCode = $enrollment->schedule ? $enrollment->schedule->schedule_code : 'N/A';
                $courseCode = $enrollment->schedule?->course?->code ?? $enrollment->course?->code ?? 'N/A';
                $studentNumber = $enrollment->user?->student_number ?? 'N/A';
                $instructor = $enrollment->schedule?->instructor?->name ?? 'N/A';

                $gradeValues = array_map(function ($item) use ($gradesByItem) {
                    $grade = $gradesByItem->get($item);
                    if (!$grade || $grade->score === null) {
                        return '';
                    }
                    return match (true) {
                        (float) $grade->score === 6.00 => 'INC',
                        (float) $grade->score === 7.00 => 'DRP',
                        default => number_format($grade->score, 2),
                    };
                }, $gradeColumns);

                fputcsv($handle, array_merge([$scheduleCode, $courseCode, $studentNumber, $instructor], $gradeValues));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

