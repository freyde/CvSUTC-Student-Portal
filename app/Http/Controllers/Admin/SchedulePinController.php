<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchedulePinController extends Controller
{
    /**
     * Simple page for admins to assign / update
     * approval PINs for specific schedule codes.
     */
    public function managePins()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        return view('admin.schedule-pins.manage');
    }

    /**
     * View all schedules with their PINs.
     */
    public function viewPins(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $query = Schedule::with(['course', 'program', 'academicYear', 'semester', 'instructor']);

        // Filter by schedule code if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('schedule_code', 'like', "%{$search}%");
        }

        $schedules = $query->orderBy('schedule_code')->paginate(50);

        return view('admin.schedule-pins.view', compact('schedules'));
    }

    public function updatePin(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

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
     * JSON helper for admins to preview schedule info
     * while typing the schedule code (any schedule).
     */
    public function scheduleInfo(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

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
}

