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
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('student_email', 100);
            $table->string('alternative_email', 100)->nullable();
            $table->integer('address_id')->nullable()->index('students_address_fk');
            $table->string('phone_number', 20)->nullable();
            $table->integer('user_id')->nullable()->index('students_user_fk');

            $table->unique(['student_email', 'alternative_email', 'phone_number'], 'students_unique');
            
            $table->foreign(['address_id'], 'students_address_FK')->references(['id'])->on('address')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'students_user_FK')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
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
