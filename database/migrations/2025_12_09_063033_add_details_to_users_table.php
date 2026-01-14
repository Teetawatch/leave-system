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
            $table->string('role')->default('employee')->after('email');
            $table->string('department')->nullable()->after('role');
            $table->string('position')->nullable()->after('department');
            $table->date('start_date')->nullable()->after('position');
            $table->foreignId('supervisor_id')->nullable()->after('start_date')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['role', 'department', 'position', 'start_date', 'supervisor_id']);
        });
    }
};
