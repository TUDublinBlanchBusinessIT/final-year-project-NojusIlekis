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
    Schema::create('media_updates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
        $table->string('file_path');
        $table->string('type'); // image or video
        $table->text('notes')->nullable();
        $table->timestamp('uploaded_at');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_updates');
    }
};
