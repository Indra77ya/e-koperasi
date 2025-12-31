<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NasabahTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('nasabahs')->truncate();
        DB::table('nasabah_loans')->truncate();

        factory(App\Models\Nasabah::class, 10)->create()->each(function ($nasabah) {
            $nasabah->loans()->saveMany(factory(App\Models\NasabahLoan::class, rand(1, 3))->make());
        });

        Schema::enableForeignKeyConstraints();
    }
}
