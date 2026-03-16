<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('carer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();

            $table->date('incident_date');
            $table->time('incident_time');

            $table->string('title');
            $table->text('description');
            $table->text('action_taken')->nullable();

            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->boolean('parent_contact_required')->default(false);
            $table->string('status')->default('open');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
