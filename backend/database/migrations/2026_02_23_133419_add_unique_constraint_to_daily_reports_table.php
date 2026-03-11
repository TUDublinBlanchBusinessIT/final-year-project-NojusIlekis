<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // STEP 1: Remove duplicates if any exist (keep the earliest record)
        // Uses a subquery compatible with both MySQL and SQLite
        DB::statement("
            DELETE FROM daily_reports
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM daily_reports
                GROUP BY child_id, date
            )
        ");

        // STEP 2: Add unique constraint
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->unique(['child_id', 'date'], 'daily_reports_child_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique('daily_reports_child_date_unique');
        });
    }
};