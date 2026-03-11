<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_parent', function (Blueprint $table) {
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('relationship_type', 40)->nullable();
            $table->boolean('legal_guardian')->default(false);
            $table->timestamps();

            $table->primary(['child_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_parent');
    }
};
