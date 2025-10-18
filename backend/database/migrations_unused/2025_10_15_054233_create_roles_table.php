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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); // ADMIN, GARANT, COMPANY, STUDENT, ANONYMOUS
            $table->string('display_name', 100); // Human readable name
            $table->text('description')->nullable(); // Role description
            $table->json('permissions')->nullable(); // Future granular permissions
            $table->boolean('is_active')->default(true); // Enable/disable roles
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
