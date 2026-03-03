<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('daily_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('child_id')->constrained()->cascadeOnDelete();
        $table->foreignId('carer_id')->constrained('users')->cascadeOnDelete();
        $table->date('date');
        $table->text('daily_report');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
