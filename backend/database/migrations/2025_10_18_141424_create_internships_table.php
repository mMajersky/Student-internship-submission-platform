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
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('company_id');
            $table->integer('garant_id')->nullable();
            $table->string('status', 50);
            $table->string('academy_year', 9);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('restrict');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
            $table->foreign('garant_id')->references('id')->on('garants')->onDelete('set null');
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
