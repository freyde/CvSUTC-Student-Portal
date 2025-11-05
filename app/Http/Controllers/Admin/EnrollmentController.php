<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportEnrollmentsFromCsv;
use App\Jobs\SplitEnrollmentsImport;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $query = Enrollment::with('user.program', 'schedule.course', 'schedule.program');

        // Search by student number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by schedule code
        if ($request->filled('schedule_code')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('schedule_code', 'like', "%{$request->schedule_code}%");
            });
        }

        $enrollments = $query->latest()->paginate(20);

        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'student_number' => ['required', 'string'],
            'schedule_code' => ['required', 'string'],
        ]);

        $student = User::where('student_number', $data['student_number'])->first();
        if (!$student) {
            return back()->withErrors(['student_number' => 'Student with this student number not found.']);
        }

        $schedule = Schedule::where('schedule_code', $data['schedule_code'])->first();
        if (!$schedule) {
            return back()->withErrors(['schedule_code' => 'Schedule with this code not found.']);
        }

        // Check if enrollment already exists
        $existing = Enrollment::where('user_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['schedule_code' => 'Student is already enrolled in this schedule.']);
        }

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $schedule->course_id,
            'schedule_id' => $schedule->id,
        ]);

        return redirect()->route('admin.enrollments.index')->with('status', 'Enrollment created successfully.');
    }

    public function destroy(Enrollment $enrollment)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')->with('status', 'Enrollment deleted successfully.');
    }

    public function importFromCsv(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'], // up to ~50MB
        ]);

        $stored = $request->file('csv_file')->store('imports/enrollments');

        // Dispatch a splitter job that creates chunk files and enqueues per-chunk import jobs
        SplitEnrollmentsImport::dispatch($stored)->onQueue('imports');

        return redirect()->route('admin.enrollments.index')->with('status', 'Import started. This may take a while. You can navigate away; data will appear as it completes.');
    }
}

