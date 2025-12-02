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
            // Status column for company registration workflow
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending')->after('user_id');
            
            // Request metadata
            $table->enum('request_source', ['public_registration', 'student_request'])->nullable()->after('status');
            $table->unsignedBigInteger('reviewed_by_garant_id')->nullable()->after('request_source');
            $table->text('rejection_reason')->nullable()->after('reviewed_by_garant_id');
            $table->timestamp('reviewed_at')->nullable()->after('rejection_reason');
            
            // Indexes for better query performance
            $table->index('status');
            $table->index('reviewed_by_garant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['reviewed_by_garant_id']);
            
            $table->dropColumn([
                'status',
                'request_source',
                'reviewed_by_garant_id',
                'rejection_reason',
                'reviewed_at',
            ]);
        });
    }
};
