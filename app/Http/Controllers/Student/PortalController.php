<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isStudent(), 403);

        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course', 'grades')->get();

        return view('student.portal.index', compact('user', 'enrollments'));
    }
}


