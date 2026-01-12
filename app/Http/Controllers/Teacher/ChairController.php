<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class ChairController extends Controller
{
    /**
     * Display the department chair dashboard.
     */
    public function index()
    {
        $this->authorizeChair();

        $user = Auth::user();
        // Get the department this user chairs (not the department they belong to)
        $department = Department::where('chair_id', $user->id)->first();

        return view('teacher.chair.dashboard', compact('department'));
    }

    /**
     * Authorize that the current user is a department chair.
     */
    private function authorizeChair(): void
    {
        $user = Auth::user();

        abort_unless(
            $user &&
            $user->isDepartmentChair(),
            403
        );
    }
}

