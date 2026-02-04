<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isStudent(), 403);

        $user = Auth::user()->load('program');
        
        // Load enrollments with all necessary relationships
        $enrollments = $user->enrollments()
            ->with([
                'course',
                'schedule.academicYear',
                'schedule.semester',
                'schedule.course',
                'grades'
            ])
            ->whereHas('schedule') // Only show enrollments with schedules
            ->get();

        // Group enrollments by academic year and semester
        $groupedEnrollments = $enrollments->groupBy(function($enrollment) {
            if ($enrollment->schedule) {
                $academicYear = $enrollment->schedule->academicYear->year ?? 'Unknown';
                $semester = $enrollment->schedule->semester->name ?? 'Unknown';
                return $academicYear . ' - ' . $semester;
            }
            return 'Unknown';
        })->sortKeysDesc(); // Sort by academic year descending

        return view('student.portal.index', compact('user', 'groupedEnrollments'));
    }

    public function schedule()
    {
        abort_unless(Auth::check() && Auth::user()->isStudent(), 403);

        $user = Auth::user();

        $activeYear = AcademicYear::where('is_active', true)->orderByDesc('id')->first();
        $currentSemester = null;
        $enrollments = collect();

        if ($activeYear) {
            $enrollmentsQuery = $user->enrollments()
                ->with([
                    'schedule.course',
                    'schedule.academicYear',
                    'schedule.semester',
                    'schedule.instructor',
                    'schedule.meetings',
                ])
                ->whereHas('schedule', function ($q) use ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                });

            // Determine "active semester" as the semester that has schedules for this year, with highest id
            $currentSemester = Semester::whereHas('schedules', function ($q) use ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                })
                ->orderByDesc('id')
                ->first();

            if ($currentSemester) {
                $enrollments = $enrollmentsQuery
                    ->whereHas('schedule', function ($q) use ($currentSemester) {
                        $q->where('semester_id', $currentSemester->id);
                    })
                    ->get();
            }
        }

        return view('student.schedule.index', [
            'user' => $user,
            'activeYear' => $activeYear,
            'currentSemester' => $currentSemester,
            'enrollments' => $enrollments,
        ]);
    }

    public function printCertificate()
    {
        abort_unless(Auth::check() && Auth::user()->isStudent(), 403);

        $user = Auth::user()->load('program');
        
        // Load enrollments with all necessary relationships
        $enrollments = $user->enrollments()
            ->with([
                'course',
                'schedule.academicYear',
                'schedule.semester',
                'schedule.course',
                'grades'
            ])
            ->whereHas('schedule') // Only show enrollments with schedules
            ->get();

        // Group enrollments by academic year and semester
        $groupedEnrollments = $enrollments->groupBy(function($enrollment) {
            if ($enrollment->schedule) {
                $academicYear = $enrollment->schedule->academicYear->year ?? 'Unknown';
                $semester = $enrollment->schedule->semester->name ?? 'Unknown';
                return $academicYear . ' - ' . $semester;
            }
            return 'Unknown';
        })->sortKeysDesc(); // Sort by academic year descending

        return view('student.portal.certificate', compact('user', 'groupedEnrollments'));
    }
}


