<?php

use Faker\Generator as Faker;
use App\Models\Comment;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'comment' => $faker->realText()
    ];
});
