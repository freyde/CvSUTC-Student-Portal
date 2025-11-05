<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\PortalController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student portal routes
    Route::prefix('student-portal')->name('student.')->group(function () {
        Route::get('/', [PortalController::class, 'index'])->name('portal.index');
    });

    // Admin routes (admin checks are in controllers)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('programs', ProgramController::class);
        Route::post('programs/import-csv', [ProgramController::class, 'importFromCsv'])->name('programs.import-csv');
        
        Route::resource('courses', CourseController::class);
        Route::post('courses/import-csv', [CourseController::class, 'importFromCsv'])->name('courses.import-csv');
        
        Route::resource('academic-years', AcademicYearController::class);
        Route::resource('semesters', SemesterController::class);
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/import-csv', [ScheduleController::class, 'importFromCsv'])->name('schedules.import-csv');
        
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::post('grades/{enrollment}/update-grade', [GradeController::class, 'updateGrade'])->name('grades.update-grade');
        Route::delete('grades/{grade}/delete-grade', [GradeController::class, 'deleteGrade'])->name('grades.delete-grade');
        
        Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::post('enrollments/import-csv', [EnrollmentController::class, 'importFromCsv'])->name('enrollments.import-csv');

        // Users management
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users/import-csv', [AdminUserController::class, 'importFromCsv'])->name('users.import-csv');
        Route::post('users/{user}/generate-password', [AdminUserController::class, 'generatePassword'])->name('users.generate-password');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
