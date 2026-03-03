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

            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            $table->foreignId('parent_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['room_id']);
            $table->index(['parent_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
