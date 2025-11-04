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
        // Admin používateľ - updateOrCreate to avoid duplicates
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'surname' => 'Adminovič',
                'role' => 'admin',
                'password' => Hash::make('password123'), // Nezabudnite zmeniť heslo na bezpečné
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Ostatní používatelia - používame updateOrInsert aby sa zabránilo duplikátom
        $otherUsers = [
            [
                'email' => 'student@example.com',
                'name' => 'Peter',
                'surname' => 'Hudec',
                'role' => 'student',
                'password' => Hash::make('password123'),
            ],
            [
                'email' => 'garant@example.com',
                'name' => 'Peter',
                'surname' => 'Zodpovedný',
                'role' => 'garant',
                'password' => Hash::make('password123'),
            ],
            [
                'email' => 'company@example.com',
                'name' => 'Eva',
                'surname' => 'Podnikavá',
                'role' => 'company',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($otherUsers as $userData) {
            DB::table('users')->updateOrInsert(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'email_verified_at' => Carbon::now(),
                    'remember_token' => Str::random(10),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}