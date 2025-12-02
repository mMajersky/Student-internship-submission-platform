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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('company_status', 50)->nullable()->after('status'); // schválený, zamietnutý
            $table->text('company_rejection_reason')->nullable()->after('company_status');
            $table->dateTime('company_validated_at')->nullable()->after('company_rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['company_status', 'company_rejection_reason', 'company_validated_at']);
        });
    }
};

