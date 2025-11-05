<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $courses = Course::latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code'],
            'title' => ['required', 'string', 'max:255'],
            'lec_unit' => ['required', 'integer', 'min:0'],
            'lab_unit' => ['required', 'integer', 'min:0'],
        ]);

        Course::create($data);

        return redirect()->route('admin.courses.index')->with('status', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code,' . $course->id],
            'title' => ['required', 'string', 'max:255'],
            'lec_unit' => ['required', 'integer', 'min:0'],
            'lab_unit' => ['required', 'integer', 'min:0'],
        ]);

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('status', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $course->delete();

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted successfully.');
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
        
        // Check if first row is a header (contains text like "code", "title", etc.)
        if (!empty($data)) {
            $firstRow = array_map('strtolower', array_map('trim', $data[0]));
            if (in_array('code', $firstRow) || in_array('course code', $firstRow)) {
                array_shift($data); // Remove header row
            }
        }
        
        // Expected format: code, title, lec_unit, lab_unit
        $imported = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }
            
            // Validate row has enough columns
            if (count($row) < 4) {
                $errors[] = "Row " . ($index + 2) . ": Insufficient columns. Expected: code, title, lec_unit, lab_unit";
                continue;
            }
            
            $validator = Validator::make([
                'code' => trim($row[0]),
                'title' => trim($row[1]),
                'lec_unit' => trim($row[2]),
                'lab_unit' => trim($row[3]),
            ], [
                'code' => ['required', 'string', 'max:50', 'unique:courses,code'],
                'title' => ['required', 'string', 'max:255'],
                'lec_unit' => ['required', 'integer', 'min:0'],
                'lab_unit' => ['required', 'integer', 'min:0'],
            ]);
            
            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 2) . " (" . ($row[0] ?? 'N/A') . "): " . implode(', ', $validator->errors()->all());
                continue;
            }
            
            try {
                Course::create($validator->validated());
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        $message = "Imported {$imported} course(s) successfully.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " error(s) occurred.";
            session()->flash('import_errors', $errors);
        }
        
        return redirect()->route('admin.courses.index')->with('status', $message);
    }
}

