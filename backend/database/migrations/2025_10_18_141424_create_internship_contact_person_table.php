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
        Schema::create('internship_contact_person', function (Blueprint $table) {
            $table->integer('internship_id');
            $table->integer('contact_person_id')->index('icp_contact_fk');

            $table->primary(['internship_id', 'contact_person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_contact_person');
    }
};
