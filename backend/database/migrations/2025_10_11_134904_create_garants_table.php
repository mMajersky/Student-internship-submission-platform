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
        Schema::create('garants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('faculty', 100)->nullable();
            $table->integer('user_id')->nullable()->index('garants_user_fk');
            
            $table->foreign(['user_id'], 'garants_user_FK')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garants');
    }
};
