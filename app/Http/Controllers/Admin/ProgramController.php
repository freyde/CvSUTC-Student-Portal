<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $programs = Program::with('courses')->latest()->get();
        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $courses = Course::orderBy('code')->get();
        return view('admin.programs.create', compact('courses'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:programs,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'majors' => ['nullable', 'string'],
            'courses' => ['nullable', 'array'],
            'courses.*' => ['exists:courses,id'],
        ]);

        $program = Program::create($data);

        if ($request->has('courses')) {
            $program->courses()->sync($request->courses);
        }

        return redirect()->route('admin.programs.index')->with('status', 'Program created successfully.');
    }

    public function edit(Program $program)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $courses = Course::orderBy('code')->get();
        $program->load('courses');
        return view('admin.programs.edit', compact('program', 'courses'));
    }

    public function update(Request $request, Program $program)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:programs,code,' . $program->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'majors' => ['nullable', 'string'],
            'courses' => ['nullable', 'array'],
            'courses.*' => ['exists:courses,id'],
        ]);

        $program->update($data);

        if ($request->has('courses')) {
            $program->courses()->sync($request->courses);
        } else {
            $program->courses()->detach();
        }

        return redirect()->route('admin.programs.index')->with('status', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $program->delete();

        return redirect()->route('admin.programs.index')->with('status', 'Program deleted successfully.');
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
        
        // Check if first row is a header (contains text like "code", "name", etc.)
        if (!empty($data)) {
            $firstRow = array_map('strtolower', array_map('trim', $data[0]));
            if (in_array('code', $firstRow) || in_array('program code', $firstRow)) {
                array_shift($data); // Remove header row
            }
        }
        
        // Expected format: code, name, description, majors
        $imported = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }
            
            // Validate row has enough columns
            if (count($row) < 2) {
                $errors[] = "Row " . ($index + 2) . ": Insufficient columns. Expected at least: code, name";
                continue;
            }
            
            $validator = Validator::make([
                'code' => trim($row[0]),
                'name' => trim($row[1]),
                'description' => isset($row[2]) ? trim($row[2]) : null,
                'majors' => isset($row[3]) ? trim($row[3]) : null,
            ], [
                'code' => ['required', 'string', 'max:50', 'unique:programs,code'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'majors' => ['nullable', 'string'],
            ]);
            
            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 2) . " (" . ($row[0] ?? 'N/A') . "): " . implode(', ', $validator->errors()->all());
                continue;
            }
            
            try {
                Program::create($validator->validated());
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        $message = "Imported {$imported} program(s) successfully.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " error(s) occurred.";
            session()->flash('import_errors', $errors);
        }
        
        return redirect()->route('admin.programs.index')->with('status', $message);
    }
}

