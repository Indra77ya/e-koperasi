<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Fix for "Target class [NasabahTableSeeder] does not exist" when composer dump-autoload hasn't been run
        if (!class_exists('NasabahTableSeeder')) {
            require_once __DIR__ . '/NasabahTableSeeder.php';
        }
        if (!class_exists('LoanSeeder')) {
            require_once __DIR__ . '/LoanSeeder.php';
        }

        $this->call([
            UsersTableSeeder::class,
            AnggotaTableSeeder::class,
            NasabahTableSeeder::class,
            LoanSeeder::class,
        ]);
    }
}
