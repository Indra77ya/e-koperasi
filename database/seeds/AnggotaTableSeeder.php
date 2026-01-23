<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AnggotaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('anggota')->truncate();
        factory(App\Models\Member::class, 100)->create();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
