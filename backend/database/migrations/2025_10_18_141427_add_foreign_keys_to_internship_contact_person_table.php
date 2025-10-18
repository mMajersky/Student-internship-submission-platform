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
        Schema::table('internship_contact_person', function (Blueprint $table) {
            $table->foreign(['contact_person_id'], 'icp_contact_FK')->references(['id'])->on('contact_persons')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['internship_id'], 'icp_internship_FK')->references(['id'])->on('internships')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_contact_person', function (Blueprint $table) {
            $table->dropForeign('icp_contact_FK');
            $table->dropForeign('icp_internship_FK');
        });
    }
};
