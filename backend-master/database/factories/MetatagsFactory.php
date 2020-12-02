<?php

use Faker\Generator as Faker;
use App\Models\Metatag;

$factory->define(Metatag::class, function (Faker $faker) {
    return [
        'keywords' => $faker->realText(),
        'title' => $faker->name(),
        'description' => $faker->realText(),
    ];
});
