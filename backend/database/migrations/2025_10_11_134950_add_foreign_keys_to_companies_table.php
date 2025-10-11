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
        Schema::table('companies', function (Blueprint $table) {
            $table->foreign(['address_id'], 'companies_address_FK')->references(['id'])->on('address')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'companies_user_FK')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign('companies_address_FK');
            $table->dropForeign('companies_user_FK');
        });
    }
};
