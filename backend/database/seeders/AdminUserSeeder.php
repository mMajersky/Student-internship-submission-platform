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
        $users = [
            [
                'name' => 'Admin',
                'surname' => 'Adminovič',
                'role' => 'admin',
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Peter',
                'surname' => 'Hudec',
                'role' => 'student',
                'email' => 'student@example.com',
            ],
            [
                'name' => 'Peter',
                'surname' => 'Zodpovedný',
                'role' => 'garant',
                'email' => 'garant@example.com',
            ],
            [
                'name' => 'Eva',
                'surname' => 'Podnikavá',
                'role' => 'company',
                'email' => 'company@example.com',
            ],
        ];

        foreach ($users as $userData) {
            DB::table('users')->updateOrInsert(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'surname' => $userData['surname'],
                    'role' => $userData['role'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => Carbon::now(),
                    'remember_token' => Str::random(10),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
