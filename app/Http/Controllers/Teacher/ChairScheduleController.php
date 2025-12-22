<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChairScheduleController extends Controller
{
    /**
     * Simple page for department chairs to assign / update
     * approval PINs for specific schedule codes.
     */
    public function managePins()
    {
        $this->authorizeChair();

        return view('teacher.chair.manage-schedule-pins');
    }

    /**
     * View all schedules in the chair's department with their PINs.
     */
    public function viewPins()
    {
        $this->authorizeChair();

        $user = Auth::user();
        $department = $user->department;

        // Get all schedules where the instructor belongs to this department
        $schedules = Schedule::whereHas('instructor', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->with(['course', 'program', 'academicYear', 'semester', 'instructor'])
        ->orderBy('schedule_code')
        ->get();

        return view('teacher.chair.view-pins', compact('schedules', 'department'));
    }

    public function updatePin(Request $request)
    {
        $this->authorizeChair();

        $data = $request->validate([
            'schedule_code' => ['required', 'string'],
            'approval_pin' => ['required', 'string', 'max:50'],
        ]);

        $schedule = Schedule::where('schedule_code', $data['schedule_code'])->first();

        if (! $schedule) {
            return back()->withErrors([
                'schedule_code' => 'Schedule code not found.',
            ])->withInput();
        }

        $schedule->update([
            'approval_pin' => $data['approval_pin'],
        ]);

        return back()->with('status', 'PIN updated for schedule '.$schedule->schedule_code.'.');
    }

    /**
     * JSON helper for chairs to preview schedule info
     * while typing the schedule code (any schedule).
     */
    public function scheduleInfo(Request $request)
    {
        $this->authorizeChair();

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

    private function authorizeChair(): void
    {
        $user = Auth::user();

        abort_unless(
            $user &&
            $user->isTeacher() &&
            $user->department &&
            $user->department->chair_id === $user->id,
            403
        );
    }
}


