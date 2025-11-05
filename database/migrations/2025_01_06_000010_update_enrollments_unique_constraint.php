<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['user_id', 'course_id']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            // Add new unique constraint for user_id and schedule_id
            $table->unique(['user_id', 'schedule_id'], 'enrollments_user_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('enrollments_user_schedule_unique');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['user_id', 'course_id']);
        });
    }
};

