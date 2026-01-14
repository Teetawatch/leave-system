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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. ลากิจ, ลาป่วย
            $table->string('slug')->unique(); // sick, personal, vacation
            $table->integer('max_days_per_year')->default(30); 
            $table->boolean('requires_advance_notice')->default(false);
            $table->integer('advance_notice_days')->default(0); // 3 for vacation
            $table->boolean('allows_retroactive')->default(false);
            $table->boolean('requires_file')->default(false); // Medical cert
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
