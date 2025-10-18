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
        Schema::create('contact_persons', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('position', 100)->nullable();
            $table->string('email', 100);
            $table->string('phone_number', 20)->nullable();
            $table->integer('company_id')->index('contact_persons_company_idx');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_persons');
    }
};
