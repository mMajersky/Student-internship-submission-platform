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
        Schema::table('students', function (Blueprint $table) {
            $table->foreign(['address_id'], 'students_address_FK')->references(['id'])->on('address')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'students_user_FK')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign('students_address_FK');
            $table->dropForeign('students_user_FK');
        });
    }
};
