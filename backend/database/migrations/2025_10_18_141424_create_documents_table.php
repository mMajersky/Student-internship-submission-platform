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
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('internship_id');
            $table->string('type', 100);
            $table->string('status', 50)->nullable();
            $table->string('file_path')->nullable();
            $table->string('name', 100)->nullable();
            $table->timestamps();

            $table->foreign('internship_id')->references('id')->on('internships')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
