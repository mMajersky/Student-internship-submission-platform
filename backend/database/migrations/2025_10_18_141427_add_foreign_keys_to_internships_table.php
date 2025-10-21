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
        Schema::table('internships', function (Blueprint $table) {
            $table->foreign(['company_id'], 'internships_company_FK')->references(['id'])->on('companies')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['garant_id'], 'internships_garant_FK')->references(['id'])->on('garants')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['student_id'], 'internships_student_FK')->references(['id'])->on('students')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropForeign('internships_company_FK');
            $table->dropForeign('internships_garant_FK');
            $table->dropForeign('internships_student_FK');
        });
    }
};
