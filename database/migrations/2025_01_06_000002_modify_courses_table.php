<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            if (Schema::hasColumn('courses', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
            }
            if (Schema::hasColumn('courses', 'program_id')) {
                $table->dropForeign(['program_id']);
            }
            
            // Drop old columns if they exist
            if (Schema::hasColumn('courses', 'teacher_id')) {
                $table->dropColumn('teacher_id');
            }
            if (Schema::hasColumn('courses', 'program_id')) {
                $table->dropColumn('program_id');
            }
        });
        
        Schema::table('courses', function (Blueprint $table) {
            // Add new columns
            $table->integer('lec_unit')->default(0)->after('title');
            $table->integer('lab_unit')->default(0)->after('lec_unit');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['lec_unit', 'lab_unit']);
            $table->unsignedBigInteger('teacher_id')->index();
            $table->unsignedBigInteger('program_id')->nullable()->index();
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
        });
    }
};

