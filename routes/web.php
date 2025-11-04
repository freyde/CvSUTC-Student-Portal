<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Student\PortalController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Fallback GET logout for environments where POST form submission is blocked
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Google OAuth
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/home', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }
    if ($user->isTeacher() || $user->isAdmin()) {
        return redirect()->route('teacher.courses.index');
    }
    return redirect()->route('student.portal.index');
})->name('home');

// Teacher routes
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');

    Route::get('/courses/{course}/enrollments', [GradeController::class, 'enrollments'])->name('grades.enrollments');
    Route::post('/courses/{course}/enrollments/{enrollment}/grade', [GradeController::class, 'upsert'])->name('grades.upsert');
});

// Student routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
});
