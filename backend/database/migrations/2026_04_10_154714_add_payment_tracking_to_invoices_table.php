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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            // unpaid, payment_submitted, approved, rejected
            $table->string('payment_proof_path')->nullable()->after('payment_status');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_proof_path');
            $table->timestamp('payment_approved_at')->nullable()->after('payment_submitted_at');
            $table->text('payment_notes')->nullable()->after('payment_approved_at');
            // parent's note e.g. bank ref
            $table->text('rejection_reason')->nullable()->after('payment_notes');
            // manager's reason if rejected
            $table->foreignId('payment_approved_by')->nullable()->after('rejection_reason')
                  ->constrained('users')->nullOnDelete();
        });

        // Sync historical paid invoices
        \DB::table('invoices')->where('status', 'paid')->update(['payment_status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_approved_by');
            $table->dropColumn([
                'payment_status', 'payment_proof_path', 'payment_submitted_at',
                'payment_approved_at', 'payment_notes', 'rejection_reason',
            ]);
        });
    }
};
