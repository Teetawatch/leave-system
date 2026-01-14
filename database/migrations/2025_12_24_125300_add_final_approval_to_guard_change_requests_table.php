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
        Schema::table('guard_change_requests', function (Blueprint $table) {
            // Final director (ผอ.) approval fields
            $table->foreignId('final_approver_id')->nullable()->after('director_approved_at')->constrained('users')->onDelete('set null');
            $table->string('final_signature')->nullable()->after('final_approver_id');
            $table->text('final_comment')->nullable()->after('final_signature');
            $table->timestamp('final_approved_at')->nullable()->after('final_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guard_change_requests', function (Blueprint $table) {
            $table->dropForeign(['final_approver_id']);
            $table->dropColumn(['final_approver_id', 'final_signature', 'final_comment', 'final_approved_at']);
        });
    }
};
