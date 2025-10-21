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
        Schema::table('contact_persons', function (Blueprint $table) {
            $table->foreign(['company_id'], 'contact_persons_company_FK')->references(['id'])->on('companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_persons', function (Blueprint $table) {
            $table->dropForeign('contact_persons_company_FK');
        });
    }
};
