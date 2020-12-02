<?php

use Faker\Generator as Faker;

$factory->define(App\Models\History::class, function (Faker $faker) {
    return [
        'status'=> $faker->numberBetween(0, 2),
        'name' => $faker->name,
        'email' => $faker->email,
        'message' => $faker->text,
        'title' => $faker->text,
        'proposal_id' => function() {
            return factory(\App\Models\Proposal::class)->create()->id;
        },
    ];
});
