<?php

use Faker\Generator as Faker;
use App\Models\Article;
use App\Models\Metatag;
use Illuminate\Http\UploadedFile;

$factory->define(Article::class, function (Faker $faker) {
    $preview_image = UploadedFile::fake()->image('preview.jpg')->store('images');
    $background_image = UploadedFile::fake()->image('background.jpg')->store('images');

    return [
        'title' => $faker->realText(),
        'type' => $faker->randomElement(['project', 'article']),
        'preview_image' => $preview_image,
        'background_image' => $background_image,
        'unique_name' => $faker->uuid(),
        'subtitle' => $faker->realText(),
        'views' => $faker->randomNumber(),
        'is_main' => 0,
        'order' => $faker->randomNumber(),
        'meta_id' => function() {
            return factory(Metatag::class)->create()->id;
        },
    ];
});
