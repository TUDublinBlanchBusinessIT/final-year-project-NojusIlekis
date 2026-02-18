<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->date('date');

            // simple MVP: present/absent
            $table->enum('status', ['present', 'absent'])->default('present');

            // optional times
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();

            // who recorded it (carer)
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // prevent duplicates: 1 attendance per child per day
            $table->unique(['child_id', 'date']);
            $table->index(['date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
