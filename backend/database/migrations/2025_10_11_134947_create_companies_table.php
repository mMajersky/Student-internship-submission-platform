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
        Schema::create('companies', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('statutary', 100);
            $table->integer('address_id')->nullable()->index('address_id');
            $table->integer('user_id')->nullable()->index('companies_user_fk');
            $table->timestamps(); // Added from feature/Generate_PDF_template

            // Foreign keys from develop
            $table->foreign(['address_id'], 'companies_address_FK')->references(['id'])->on('address')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'companies_user_FK')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};