<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Show form where a teacher enters a schedule code for the class
     * they want to input grades for. Public route - no login required.
     */
    public function selectSchedule()
    {
        return view('teacher.grades.select-schedule');
    }

    /**
     * Show enrollments/grades for a schedule selected by schedule code.
     * Public route - no login required. Anyone with the schedule code can access.
     */
    public function showSchedule(Request $request)
    {
        $data = $request->validate([
            'schedule_code' => ['required', 'string'],
        ]);

        $schedule = Schedule::where('schedule_code', $data['schedule_code'])
            ->with(['course', 'academicYear', 'semester', 'enrollments.user', 'enrollments.grades'])
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
     * while typing the schedule code. Public route - no login required.
     */
    public function scheduleInfo(Request $request)
    {
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
     * Save or update a grade. Public route - no login required.
     * Protected by schedule code access and finalization status.
     */
    public function upsert(Request $request, Schedule $schedule, Enrollment $enrollment)
    {
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

    /**
     * Final confirmation of grades for a schedule using the schedule's unique PIN.
     * Public route - no login required. PIN is required to finalize.
     */
    public function finalize(Request $request, Schedule $schedule)
    {
        abort_if($schedule->finalized_at !== null, 403, 'Grades for this schedule are already finalized.');

        $data = $request->validate([
            'approval_pin' => ['required', 'string'],
        ]);

        // PIN must match the schedule's unique approval_pin
        if (! $schedule->approval_pin) {
            return back()->withErrors([
                'approval_pin' => 'This schedule does not have a PIN assigned. Please contact an administrator.',
            ]);
        }

        if ($schedule->approval_pin !== $data['approval_pin']) {
            return back()->withErrors([
                'approval_pin' => 'Invalid PIN for this schedule. Please contact your department chair.',
            ]);
        }

        $schedule->update([
            'finalized_at' => now(),
            'finalized_by' => Auth::id(), // Will be null if not logged in, which is fine
        ]);

        return back()->with('status', 'Grades finalized with department approval.');
    }

}
