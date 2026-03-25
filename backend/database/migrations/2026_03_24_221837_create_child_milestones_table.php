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
        Schema::create('child_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('milestone_id')->constrained()->onDelete('cascade');
            $table->foreignId('observed_by')->constrained('users')->onDelete('cascade');
            $table->date('observed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['child_id', 'milestone_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_milestones');
    }
};
