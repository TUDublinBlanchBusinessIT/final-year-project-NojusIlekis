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
        DB::statement("
            DELETE dr1 FROM daily_reports dr1
            INNER JOIN daily_reports dr2
            WHERE 
                dr1.id > dr2.id
                AND dr1.child_id = dr2.child_id
                AND dr1.date = dr2.date
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