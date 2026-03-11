<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['room_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
