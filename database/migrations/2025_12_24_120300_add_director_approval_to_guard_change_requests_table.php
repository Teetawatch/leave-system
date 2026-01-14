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
            $table->foreignId('director_approver_id')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            $table->string('director_signature')->nullable()->after('director_approver_id');
            $table->text('director_comment')->nullable()->after('director_signature');
            $table->timestamp('director_approved_at')->nullable()->after('director_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guard_change_requests', function (Blueprint $table) {
            $table->dropForeign(['director_approver_id']);
            $table->dropColumn(['director_approver_id', 'director_signature', 'director_comment', 'director_approved_at']);
        });
    }
};
