<?php

use Faker\Generator as Faker;
use App\Models\ArticleBlock;
use Illuminate\Http\UploadedFile;

$factory->define(ArticleBlock::class, function (Faker $faker) {
    $data = array();

    $data['type'] = $faker->randomElement(['image', 'text']);

    if($data['type'] === 'image') {
        $data['image'] = UploadedFile::fake()->image($faker->realText(15) . '.jpg')->store('images');
    } else {
        $data['text'] = $faker->realText();
    }

    return $data;
});
