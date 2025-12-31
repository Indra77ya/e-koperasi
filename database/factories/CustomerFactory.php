<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'nik' => $faker->unique()->numerify('################'),
        'nama' => $faker->name,
        'alamat' => $faker->address,
        'no_hp' => $faker->phoneNumber,
        'pekerjaan' => $faker->jobTitle,
        'info_bisnis' => $faker->catchPhrase,
        'status_risiko' => $faker->randomElement(['safe', 'warning', 'blacklist']),
    ];
});
