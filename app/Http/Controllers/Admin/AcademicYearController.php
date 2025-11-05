<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $academicYears = AcademicYear::latest()->get();
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'year' => ['required', 'string', 'max:50', 'unique:academic_years,year'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // If setting this as active, deactivate others
        if ($request->boolean('is_active')) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($data);

        return redirect()->route('admin.academic-years.index')->with('status', 'Academic year created successfully.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'year' => ['required', 'string', 'max:50', 'unique:academic_years,year,' . $academicYear->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // If setting this as active, deactivate others
        if ($request->boolean('is_active')) {
            AcademicYear::where('id', '!=', $academicYear->id)->where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('admin.academic-years.index')->with('status', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')->with('status', 'Academic year deleted successfully.');
    }
}

