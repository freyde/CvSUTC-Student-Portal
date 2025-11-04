<?php

namespace Database\Seeders;

use App\Models\User;
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

        // Create a demo course and enrollment
        $course = \App\Models\Course::create([
            'code' => 'CS101',
            'title' => 'Intro to Programming',
            'teacher_id' => $teacher->id,
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
