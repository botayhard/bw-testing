<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Proposal::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'phone' => $faker->phoneNumber,
        'email' => $faker->unique()->safeEmail,
        'description' => $faker->text(20),
        'status' => 'ok'
    ];
});
