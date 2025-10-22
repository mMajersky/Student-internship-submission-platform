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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('internship_id')->index('comments_internship_idx');
            $table->integer('garant_id')->index('comments_garant_idx');
            $table->text('content');
            $table->enum('comment_type', ['approval', 'rejection', 'correction', 'general'])->default('general');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('internship_id')->references('id')->on('internships')->onDelete('cascade');
            $table->foreign('garant_id')->references('id')->on('garants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
