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
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('student_email', 100);
            $table->string('alternative_email', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('study_level', 50);
            $table->string('state', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('house_number', 20)->nullable();
            $table->timestamps();

            $table->unique(['student_email', 'alternative_email', 'phone_number'], 'students_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
