<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use a subquery UPDATE compatible with both MySQL and SQLite
        DB::statement("
            UPDATE attendances
            SET room_id = (SELECT room_id FROM children WHERE children.id = attendances.child_id)
            WHERE room_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
