<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            // Admin používateľ
            [
                'name' => 'Admin',
                'surname' => 'Adminovič',
                'role' => 'admin',
                'password' => Hash::make('password'), // Nezabudnite zmeniť heslo na bezpečné
                'email' => 'admin@example.com',
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Študent používateľ
            [
                'name' => 'Peter',
                'surname' => 'Hudec',
                'role' => 'student',
                'password' => Hash::make('password'),
                'email' => 'student@example.com',
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Garant používateľ
            [
                'name' => 'Peter',
                'surname' => 'Zodpovedný',
                'role' => 'garant',
                'password' => Hash::make('password'),
                'email' => 'garant@example.com',
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Zástupca spoločnosti používateľ
            [
                'name' => 'Eva',
                'surname' => 'Podnikavá',
                'role' => 'company_representative',
                'password' => Hash::make('password'),
                'email' => 'company@example.com',
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}