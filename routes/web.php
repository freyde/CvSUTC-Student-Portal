<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\PortalController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\ChairScheduleController;
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
        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.update-password');
    });

    // Admin routes (admin checks are in controllers)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('programs', ProgramController::class);
        Route::post('programs/import-csv', [ProgramController::class, 'importFromCsv'])->name('programs.import-csv');
        
        Route::resource('courses', CourseController::class);
        Route::post('courses/import-csv', [CourseController::class, 'importFromCsv'])->name('courses.import-csv');
        
        Route::resource('academic-years', AcademicYearController::class);
        Route::resource('semesters', SemesterController::class);
        Route::resource('departments', DepartmentController::class)->except(['show']);
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/import-csv', [ScheduleController::class, 'importFromCsv'])->name('schedules.import-csv');
        Route::post('schedules/generate-all-pins', [ScheduleController::class, 'generateAllPins'])->name('schedules.generate-all-pins');
        
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
        Route::post('users/{user}/view-password', [AdminUserController::class, 'viewPassword'])->name('users.view-password');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });

    // Teacher routes (require auth)
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('courses/create', [TeacherCourseController::class, 'create'])->name('courses.create');
        Route::post('courses', [TeacherCourseController::class, 'store'])->name('courses.store');

        // Department chair routes
        Route::get('chair/schedule-pins', [ChairScheduleController::class, 'managePins'])->name('chair.schedule-pins');
        Route::get('chair/view-pins', [ChairScheduleController::class, 'viewPins'])->name('chair.view-pins');
        Route::post('chair/schedule-pins', [ChairScheduleController::class, 'updatePin'])->name('chair.schedule-pins.update');
        Route::get('chair/schedule-info', [ChairScheduleController::class, 'scheduleInfo'])->name('chair.schedule-info');
    });
});

// Public grade entry routes (no login required, protected by PIN)
Route::prefix('grades')->name('grades.')->group(function () {
    Route::get('/', [TeacherGradeController::class, 'selectSchedule'])->name('select-schedule');
    Route::post('/show', [TeacherGradeController::class, 'showSchedule'])->name('show-schedule');
    Route::get('/schedule-info', [TeacherGradeController::class, 'scheduleInfo'])->name('schedule-info');
    Route::post('/{schedule}/enrollments/{enrollment}', [TeacherGradeController::class, 'upsert'])->name('upsert');
    Route::post('/{schedule}/finalize', [TeacherGradeController::class, 'finalize'])->name('finalize');
});

require __DIR__.'/auth.php';
