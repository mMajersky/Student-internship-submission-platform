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
        Schema::create('internships', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('student_id')->index('internships_student_fk');
            $table->integer('company_id')->index('internships_company_fk');
            $table->integer('garant_id')->nullable()->index('internships_garant_fk');
            $table->string('status', 50);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            
            $table->foreign(['company_id'], 'internships_company_FK')->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['garant_id'], 'internships_garant_FK')->references(['id'])->on('garants')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['student_id'], 'internships_student_FK')->references(['id'])->on('students')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
