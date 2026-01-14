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
            $table->foreignId('approver_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->string('approval_signature')->nullable()->after('approver_id');
            $table->text('approval_comment')->nullable()->after('approval_signature');
            $table->timestamp('approved_at')->nullable()->after('approval_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guard_change_requests', function (Blueprint $table) {
            $table->dropForeign(['approver_id']);
            $table->dropColumn(['approver_id', 'approval_signature', 'approval_comment', 'approved_at']);
        });
    }
};
