<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Show form where a teacher enters a schedule code for the class
     * they want to input grades for.
     */
    public function selectSchedule()
    {
        $this->authorizeAccess();
        
        return view('teacher.grades.select-schedule');
    }

    /**
     * Show enrollments/grades for a schedule selected by schedule code.
     */
    public function showSchedule(Request $request)
    {
        $this->authorizeAccess();
        
        $data = $request->validate([
            'schedule_code' => ['required', 'string'],
        ]);

        $schedule = Schedule::where('schedule_code', $data['schedule_code'])
            ->with(['course', 'academicYear', 'semester', 'instructor', 'enrollments.user', 'enrollments.grades'])
            ->first();

        if (! $schedule) {
            return back()->withErrors([
                'schedule_code' => 'Schedule code not found. Please check and try again.',
            ]);
        }

        $enrollments = $schedule->enrollments;

        return view('teacher.grades.enrollments', [
            'schedule' => $schedule,
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * JSON helper so users can see basic schedule info
     * while typing the schedule code.
     */
    public function scheduleInfo(Request $request)
    {
        $this->authorizeAccess();
        
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $schedule = Schedule::where('schedule_code', $request->code)
            ->with(['course', 'program', 'academicYear', 'semester'])
            ->first();

        if (! $schedule) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'schedule_code' => $schedule->schedule_code,
            'course_code' => optional($schedule->course)->code,
            'course_title' => optional($schedule->course)->title,
            'program' => optional($schedule->program)->code,
            'year' => $schedule->year,
            'section' => $schedule->section,
        ]);
    }

    /**
     * Save or update a grade.
     * Only the assigned instructor (or admin/department chair) can upload grades.
     */
    public function upsert(Request $request, Schedule $schedule, Enrollment $enrollment)
    {
        $this->authorizeAccess();
        $this->authorizeInstructorAccess($schedule);
        
        abort_unless($enrollment->schedule_id === $schedule->id, 404);
        abort_if($schedule->finalized_at !== null, 403, 'Grades for this schedule have already been finalized.');

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

    public function viewSchedule (Request $request, Schedule $schedule) {
        return view('teacher.grades.select-schedule');
    }

    /**
     * Save all grades and finalize the schedule using the schedule's unique PIN.
     */
    public function finalize(Request $request, Schedule $schedule)
    {
        $this->authorizeAccess();
        $this->authorizeInstructorAccess($schedule);
        
        abort_if($schedule->finalized_at !== null, 403, 'Grades for this schedule are already finalized.');

        $validGrades = [
            '1.00', '1.25', '1.50', '1.75', '2.00', '2.25', '2.50', '2.75', 
            '3.00', '4.00', '6.00', '7.00', '5.00'
        ];

        $data = $request->validate([
            'approval_pin' => ['required', 'string'],
            'grades' => ['required', 'array'],
            'grades.*' => ['nullable', 'string', 'in:' . implode(',', $validGrades)],
        ]);

        // PIN must match the schedule's unique approval_pin
        if (! $schedule->approval_pin) {
            return redirect()
                ->route('grades.view-schedule')
                ->withErrors([
                    'approval_pin' => 'Invalid PIN for this schedule.',
            ])->withInput();
        }

        if ($schedule->approval_pin !== $data['approval_pin']) {
            return redirect()
                ->route('grades.view-schedule')
                ->withErrors([
                    'approval_pin' => 'Invalid PIN for this schedule.',
            ])->withInput();
        }

        // Save all grades before finalizing
        $enrollments = $schedule->enrollments;
        foreach ($enrollments as $enrollment) {
            $gradeValue = $data['grades'][$enrollment->id] ?? null;
            
            if ($gradeValue !== null && $gradeValue !== '') {
                Grade::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'item' => 'Final',
                    ],
                    [
                        'score' => $gradeValue,
                    ],
                );
            }
        }

        // Finalize the schedule
        $schedule->update([
            'finalized_at' => now(),
            'finalized_by' => Auth::id(),
        ]);

        return redirect()->route('grades.select-schedule')
            ->with('status', 'Grades saved and finalized successfully.');
    }

    /**
     * Authorize that the current user is an admin, teacher, or department chair.
     */
    private function authorizeAccess(): void
    {
        $user = Auth::user();

        abort_unless(
            $user &&
            ($user->isAdmin() || $user->isTeacher() || $user->isDepartmentChair()),
            403,
            'Access denied. Only administrators, teachers, and department chairs can access this page.'
        );
    }

    /**
     * Authorize that the current user is the assigned instructor for the schedule,
     * or is an admin. Only the assigned instructor (or admin) can upload grades.
     */
    private function authorizeInstructorAccess(Schedule $schedule): void
    {
        $user = Auth::user();

        // Admins can always upload grades for all schedules
        if ($user->isAdmin()) {
            return;
        }

        // Check if grade upload is allowed for teachers and department chairs
        $allowTeacherGradeUpload = Setting::get('allow_teacher_grade_upload', '1') === '1';
        
        if (!$allowTeacherGradeUpload) {
            abort(403, 'Grade uploads are currently disabled for teachers and department chairs. Please contact an administrator.');
        }

        // Only the assigned instructor can upload grades
        abort_unless(
            $schedule->instructor_id === $user->id,
            403,
            'You are not authorized to upload grades for this schedule. Only the assigned instructor can upload grades.'
        );
    }

}
