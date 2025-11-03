<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientRepository = app(ClientRepository::class);
        
        // Vytvoríme personal access client, ak ešte neexistuje
        // Passport používa tento client pre createToken() metódu
        try {
            $clientRepository->createPersonalAccessGrantClient('Personal Access Client', 'users');
            $this->command->info('Passport personal access client created successfully.');
        } catch (\Exception $e) {
            // Ak už existuje, ignorujeme chybu
            $this->command->warn('Passport personal access client may already exist.');
        }
    }
}

