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
        Schema::create('company_requests', function (Blueprint $table) {
            $table->id();
            
            // Company information
            $table->string('company_name', 100);
            $table->string('state', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('house_number', 20)->nullable();
            
            // Contact person information
            $table->string('contact_person_name', 100);
            $table->string('contact_person_surname', 100);
            $table->string('contact_person_email', 100);
            $table->string('contact_person_phone', 50)->nullable();
            
            // Request metadata
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('request_source', ['public_registration', 'student_request'])->default('public_registration');
            $table->unsignedBigInteger('requested_by_user_id')->nullable(); // Student who requested (if from internship form)
            $table->unsignedBigInteger('reviewed_by_garant_id')->nullable(); // Garant who approved/rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Link to created company (when approved)
            $table->unsignedInteger('company_id')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('requested_by_user_id');
            $table->index('reviewed_by_garant_id');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_requests');
    }
};
