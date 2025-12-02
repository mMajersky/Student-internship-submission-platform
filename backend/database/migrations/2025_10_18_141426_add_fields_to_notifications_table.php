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
        Schema::table('notifications', function (Blueprint $table) {
            // Pridať stĺpce ktoré chýbajú v existujúcej tabuľke
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type', 50)->nullable()->after('user_id'); // typ notifikácie
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('message'); // dodatočné dáta (internship_id, atď.)
            }
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read'); // kedy bola prečítaná
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'data', 'read_at']);
        });
    }
};
