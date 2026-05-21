<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedInteger('max_children_per_staff')->default(6)->after('capacity');
        });

        DB::table('rooms')->where('name', 'Bumblebees')->update(['max_children_per_staff' => 5]);
        DB::table('rooms')->where('name', 'Ladybirds')->update(['max_children_per_staff' => 6]);
        DB::table('rooms')->where('name', 'Caterpillars')->update(['max_children_per_staff' => 3]);
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('max_children_per_staff');
        });
    }
};
