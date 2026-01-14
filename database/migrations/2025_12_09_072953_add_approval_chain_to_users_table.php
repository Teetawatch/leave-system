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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('deputy_id')->nullable()->after('supervisor_id')->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->after('deputy_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['deputy_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['deputy_id', 'manager_id']);
        });
    }
};
