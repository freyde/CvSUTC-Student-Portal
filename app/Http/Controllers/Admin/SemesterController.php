<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SemesterController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $semesters = Semester::latest()->get();
        return view('admin.semesters.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:semesters,code'],
        ]);

        Semester::create($data);

        return redirect()->route('admin.semesters.index')->with('status', 'Semester created successfully.');
    }

    public function update(Request $request, Semester $semester)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:semesters,code,' . $semester->id],
        ]);

        $semester->update($data);

        return redirect()->route('admin.semesters.index')->with('status', 'Semester updated successfully.');
    }

    public function destroy(Semester $semester)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $semester->delete();

        return redirect()->route('admin.semesters.index')->with('status', 'Semester deleted successfully.');
    }
}

