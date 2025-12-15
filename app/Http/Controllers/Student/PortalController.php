<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
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
}


