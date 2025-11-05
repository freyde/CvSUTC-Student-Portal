<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $query = Enrollment::with('user', 'course', 'schedule', 'grades');

        // Search by student number only
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(20);

        return view('admin.grades.index', compact('enrollments'));
    }

    public function updateGrade(Request $request, Enrollment $enrollment)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $data = $request->validate([
            'item' => ['required', 'string', 'max:100'],
            'score' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        Grade::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'item' => $data['item'],
            ],
            [
                'score' => $data['score'] ?? null,
            ]
        );

        return back()->with('status', 'Grade updated successfully.');
    }

    public function deleteGrade(Grade $grade)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
        
        $grade->delete();

        return back()->with('status', 'Grade deleted successfully.');
    }
}

