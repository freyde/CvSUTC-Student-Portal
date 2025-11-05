<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $teacher = User::factory()->create([
            'name' => 'Teacher Tina',
            'email' => 'teacher@example.com',
            'role' => 'teacher',
            'password' => 'password',
        ]);

        $student = User::factory()->create([
            'name' => 'Student Sam',
            'email' => 'student@example.com',
            'role' => 'student',
            'student_number' => 'S-0001',
            'password' => 'password',
        ]);

        // Create academic years
        $academicYear2024 = AcademicYear::create([
            'year' => '2024-2025',
            'is_active' => true,
        ]);

        $academicYear2023 = AcademicYear::create([
            'year' => '2023-2024',
            'is_active' => false,
        ]);

        // Create semesters
        $firstSemester = Semester::create([
            'name' => 'First Semester',
            'code' => '1ST',
        ]);

        $secondSemester = Semester::create([
            'name' => 'Second Semester',
            'code' => '2ND',
        ]);

        $summerSemester = Semester::create([
            'name' => 'Summer',
            'code' => 'SUMMER',
        ]);

        // Create a demo course and enrollment
        $course = \App\Models\Course::create([
            'code' => 'CS101',
            'title' => 'Intro to Programming',
            'lec_unit' => 3,
            'lab_unit' => 1,
        ]);

        $enrollment = \App\Models\Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        \App\Models\Grade::create([
            'enrollment_id' => $enrollment->id,
            'item' => 'Final',
            'score' => 95,
        ]);
    }
}
