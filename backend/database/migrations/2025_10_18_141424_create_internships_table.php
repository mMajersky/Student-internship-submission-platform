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
        Schema::create('internships', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('student_id')->index('internships_student_idx');
            $table->integer('company_id')->index('internships_company_idx');
            $table->integer('garant_id')->nullable()->index('internships_garant_idx');
            $table->string('status', 50);
            $table->string('academy_year', 9);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
