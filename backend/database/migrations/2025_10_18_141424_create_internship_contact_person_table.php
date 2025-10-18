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
        Schema::create('internship_contact_person', function (Blueprint $table) {
            $table->integer('internship_id');
            $table->integer('contact_person_id');

            $table->primary(['internship_id', 'contact_person_id']);
            $table->foreign('internship_id')->references('id')->on('internships')->onDelete('cascade');
            $table->foreign('contact_person_id')->references('id')->on('contact_persons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_contact_person');
    }
};
