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
            $table->integer('id', true);
            $table->integer('internship_id')->index('documents_internship_fk');
            $table->string('type', 100);
            $table->string('status', 50)->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->string('name', 100)->nullable();
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
