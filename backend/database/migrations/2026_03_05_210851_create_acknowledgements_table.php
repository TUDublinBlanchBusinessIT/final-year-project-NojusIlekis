<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acknowledgements', function (Blueprint $table) {
            $table->id();

            // what is being acknowledged
            $table->string('record_type');            // e.g. 'daily_report'
            $table->unsignedBigInteger('record_id');  // e.g. daily_reports.id

            // who must acknowledge it
            $table->unsignedBigInteger('parent_id');  // users.id (role = parent)

            // signing status
            $table->string('status')->default('pending'); // pending | acknowledged
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_name')->nullable();

            $table->timestamps();

            // helpful indexes
            $table->index(['record_type', 'record_id']);
            $table->index(['parent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acknowledgements');
    }
};