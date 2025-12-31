<?php

use Faker\Generator as Faker;

$factory->define(App\Models\NasabahLoan::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2, 1000000, 50000000),
        'loan_date' => $faker->dateTimeBetween('-1 year', 'now'),
        'due_date' => $faker->dateTimeBetween('now', '+1 year'),
        'status' => $faker->randomElement(['pending', 'paid', 'overdue']),
        'notes' => $faker->sentence,
    ];
});
