<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::ADMIN,
                'display_name' => 'Administrator',
                'description' => 'System administrator with full access to all features',
                'permissions' => [
                    'manage_users',
                    'manage_announcements',
                    'manage_internships',
                    'manage_companies',
                    'view_reports',
                    'system_settings'
                ],
                'is_active' => true,
            ],
            [
                'name' => Role::GARANT,
                'display_name' => 'Garant',
                'description' => 'Academic supervisor responsible for internship oversight',
                'permissions' => [
                    'manage_announcements',
                    'manage_internships',
                    'approve_internships',
                    'view_student_progress',
                    'manage_companies'
                ],
                'is_active' => true,
            ],
            [
                'name' => Role::COMPANY,
                'display_name' => 'Company Representative',
                'description' => 'Company representative who can post internship opportunities',
                'permissions' => [
                    'create_internships',
                    'manage_own_internships',
                    'view_applications',
                    'manage_company_profile'
                ],
                'is_active' => true,
            ],
            [
                'name' => Role::STUDENT,
                'display_name' => 'Student',
                'description' => 'Student who can apply for internships and submit reports',
                'permissions' => [
                    'apply_internships',
                    'submit_reports',
                    'view_own_applications',
                    'manage_profile'
                ],
                'is_active' => true,
            ],
            [
                'name' => Role::ANONYMOUS,
                'display_name' => 'Anonymous User',
                'description' => 'Unauthenticated user with limited access',
                'permissions' => [
                    'view_public_announcements',
                    'view_public_internships'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
