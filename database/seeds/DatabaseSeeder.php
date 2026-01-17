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
        // Require all seeders to be safe
        foreach (glob(__DIR__ . '/*.php') as $filename) {
            require_once $filename;
        }

        $this->call([
            UsersTableSeeder::class,
            AnggotaTableSeeder::class,
            NasabahTableSeeder::class,
            LoanSeeder::class,
            CollectionSeeder::class,
            CoaSeeder::class,
            JournalSeeder::class,
            ReportSeeder::class,
            MutationSeeder::class,
            BankInterestSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
