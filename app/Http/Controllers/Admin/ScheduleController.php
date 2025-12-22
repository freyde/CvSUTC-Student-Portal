<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $query = Schedule::with('course', 'program', 'academicYear', 'semester', 'instructor');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('schedule_code', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhereHas('course', function($q) use ($search) {
                      $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('program', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('instructor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Academic Year filter
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Semester filter
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Program filter
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $schedules = $query->latest()->paginate(20);
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::orderBy('code')->get();
        $programs = Program::orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'academicYears', 'semesters', 'programs'));
    }

    public function create()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $courses = Course::orderBy('code')->get();
        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::orderBy('code')->get();
        $instructors = User::whereIn('role', ['teacher', 'admin'])->orderBy('name')->get();

        return view('admin.schedules.create', compact('courses', 'programs', 'academicYears', 'semesters', 'instructors'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'schedule_code' => ['required', 'string', 'max:50', 'unique:schedules,schedule_code'],
            'course_id' => ['required', 'exists:courses,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'year' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'instructor_id' => ['nullable', 'exists:users,id'],
        ]);

        Schedule::create($data);

        return redirect()->route('admin.schedules.index')->with('status', 'Schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $courses = Course::orderBy('code')->get();
        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::orderBy('code')->get();
        $instructors = User::whereIn('role', ['teacher', 'admin'])->orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'courses', 'programs', 'academicYears', 'semesters', 'instructors'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'schedule_code' => ['required', 'string', 'max:50', 'unique:schedules,schedule_code,' . $schedule->id],
            'course_id' => ['required', 'exists:courses,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'year' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'instructor_id' => ['nullable', 'exists:users,id'],
        ]);

        $schedule->update($data);

        return redirect()->route('admin.schedules.index')->with('status', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('status', 'Schedule deleted successfully.');
    }

    public function importFromCsv(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        
        // Check if first row is a header
        if (!empty($data)) {
            $firstRow = array_map('strtolower', array_map('trim', $data[0]));
            $headerKeywords = ['schedule', 'course', 'program', 'academic', 'semester', 'year', 'section', 'instructor'];
            $hasHeader = false;
            foreach ($headerKeywords as $keyword) {
                if (in_array($keyword, $firstRow)) {
                    $hasHeader = true;
                    break;
                }
            }
            if ($hasHeader) {
                array_shift($data); // Remove header row
            }
        }
        
        // Expected format: schedule_code, course_code, program_code (optional), academic_year, semester_code, year (optional), section (optional), instructor_email (optional)
        $imported = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }
            
            // Validate row has minimum required columns (schedule_code, course_code, academic_year, semester_code)
            if (count($row) < 5) {
                $errors[] = "Row " . ($index + 2) . ": Insufficient columns. Expected at least: schedule_code, course_code, program_code (optional), academic_year, semester_code";
                continue;
            }
            
            $scheduleCode = trim($row[0]);
            $courseCode = trim($row[1]);
            $programCode = isset($row[2]) && trim($row[2]) !== '' ? trim($row[2]) : null;
            $academicYearStr = trim($row[3]);
            $semesterCode = trim($row[4]);
            $year = isset($row[5]) && trim($row[5]) !== '' ? trim($row[5]) : null;
            $section = isset($row[6]) && trim($row[6]) !== '' ? trim($row[6]) : null;
            $instructorEmail = isset($row[7]) && trim($row[7]) !== '' ? trim($row[7]) : null;
            
            // Look up IDs
            $course = Course::where('code', $courseCode)->first();
            if (!$course) {
                $errors[] = "Row " . ($index + 2) . ": Course code '{$courseCode}' not found";
                continue;
            }
            
            $program = null;
            if ($programCode) {
                $program = Program::where('code', $programCode)->first();
                if (!$program) {
                    $errors[] = "Row " . ($index + 2) . ": Program code '{$programCode}' not found";
                    continue;
                }
            }
            
            $academicYear = AcademicYear::where('year', $academicYearStr)->first();
            if (!$academicYear) {
                $errors[] = "Row " . ($index + 2) . ": Academic year '{$academicYearStr}' not found";
                continue;
            }
            
            $semester = Semester::where('code', $semesterCode)->first();
            if (!$semester) {
                $errors[] = "Row " . ($index + 2) . ": Semester code '{$semesterCode}' not found";
                continue;
            }
            
            $instructor = null;
            if ($instructorEmail) {
                $instructor = User::where('email', $instructorEmail)
                    ->whereIn('role', ['teacher', 'admin'])
                    ->first();
                if (!$instructor) {
                    $errors[] = "Row " . ($index + 2) . ": Instructor with email '{$instructorEmail}' not found or not a teacher/admin";
                    continue;
                }
            }
            
            $validator = Validator::make([
                'schedule_code' => $scheduleCode,
                'course_id' => $course->id,
                'program_id' => $program?->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'year' => $year,
                'section' => $section,
                'instructor_id' => $instructor?->id,
            ], [
                'schedule_code' => ['required', 'string', 'max:50', 'unique:schedules,schedule_code'],
                'course_id' => ['required', 'exists:courses,id'],
                'program_id' => ['nullable', 'exists:programs,id'],
                'academic_year_id' => ['required', 'exists:academic_years,id'],
                'semester_id' => ['required', 'exists:semesters,id'],
                'year' => ['nullable', 'string', 'max:50'],
                'section' => ['nullable', 'string', 'max:50'],
                'instructor_id' => ['nullable', 'exists:users,id'],
            ]);
            
            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 2) . " (" . $scheduleCode . "): " . implode(', ', $validator->errors()->all());
                continue;
            }
            
            try {
                Schedule::create($validator->validated());
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        $message = "Imported {$imported} schedule(s) successfully.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " error(s) occurred.";
            session()->flash('import_errors', $errors);
        }
        
        return redirect()->route('admin.schedules.index')->with('status', $message);
    }

    /**
     * Generate approval PINs for all schedules that don't have one yet.
     */
    public function generateAllPins()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $schedulesWithoutPin = Schedule::whereNull('approval_pin')->orWhere('approval_pin', '')->get();
        $generated = 0;

        foreach ($schedulesWithoutPin as $schedule) {
            // Generate a random 6-digit PIN
            $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness (very unlikely but check anyway)
            while (Schedule::where('approval_pin', $pin)->exists()) {
                $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            }

            $schedule->update(['approval_pin' => $pin]);
            $generated++;
        }

        $message = $generated > 0 
            ? "Generated PINs for {$generated} schedule(s)." 
            : "All schedules already have PINs assigned.";

        return redirect()->route('admin.schedules.index')->with('status', $message);
    }
}

