<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Nasabah::class, function (Faker $faker) {
    return [
        'nik' => $faker->unique()->numerify('################'),
        'nama' => $faker->name,
        'alamat' => $faker->address,
        'no_hp' => $faker->phoneNumber,
        'pekerjaan' => $faker->randomElement(['PNS', 'Wiraswasta', 'Petani', 'Buruh', 'Guru', 'Pedagang', 'TNI/Polri', 'Dokter', 'Karyawan Swasta', 'Sopir', 'Nelayan']),
        'usaha' => $faker->company,
        'status' => $faker->randomElement(['aman', 'blacklist', 'berisiko']),
    ];
});
