<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Voláme nové seedery v správnom logickom poradí.
        // Laravel ich spustí presne takto, keď použiješ príkaz `php artisan db:seed`.
        $this->call([
            // 1. Najprv vytvoríme všetkých používateľov (admin, student, garant...)
            AdminUserSeeder::class, 
            
            // 2. Potom vytvoríme študenta a prepojíme ho s používateľom
            StudentSeeder::class,

            // 3. Potom vytvoríme garanta, firmu a kontaktnú osobu
            DemoEntitiesSeeder::class,

            // 4. Až nakoniec, keď máme všetko pripravené, vytvoríme samotné praxe
            InternshipSeeder::class,
        ]);
    }
}