<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('grades')->whereIn('item', ['Prelim', 'Midterm'])->delete();
    }

    public function down(): void
    {
        // Cannot restore deleted grades
    }
};
