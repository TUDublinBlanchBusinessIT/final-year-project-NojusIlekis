<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_user', function (Blueprint $table) {
            if (!Schema::hasColumn('room_user', 'start_date')) {
                $table->date('start_date')->default(now()->toDateString())->after('user_id');
            }

            if (!Schema::hasColumn('room_user', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (!Schema::hasColumn('room_user', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('end_date');
            }

            if (!Schema::hasColumn('room_user', 'created_at') && !Schema::hasColumn('room_user', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_user', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('room_user', 'start_date')) {
                $drops[] = 'start_date';
            }

            if (Schema::hasColumn('room_user', 'end_date')) {
                $drops[] = 'end_date';
            }

            if (Schema::hasColumn('room_user', 'is_primary')) {
                $drops[] = 'is_primary';
            }

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }

            if (Schema::hasColumn('room_user', 'created_at') && Schema::hasColumn('room_user', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
};