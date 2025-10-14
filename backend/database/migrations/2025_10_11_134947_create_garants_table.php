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
            $table->unsignedBigInteger('user_id')->nullable()->index('garants_user_fk');
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
