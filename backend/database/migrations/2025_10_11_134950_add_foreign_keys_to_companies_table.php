<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!$this->foreignKeyExists('companies', 'companies_address_FK')) {
                $table->foreign(['address_id'], 'companies_address_FK')->references(['id'])->on('address')->onUpdate('cascade')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('companies', 'companies_user_FK')) {
                $table->foreign(['user_id'], 'companies_user_FK')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
            }
        });
    }

    private function foreignKeyExists($table, $constraintName)
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);
        
        return !empty($foreignKeys);
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
