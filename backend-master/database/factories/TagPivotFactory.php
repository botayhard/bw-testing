<?php

use Faker\Generator as Faker;
use App\User;

$factory->define(App\Models\TagPivot::class, function (Faker $faker) {
    $new_user = factory(User::class)->create();
    return [
        'article_id' => function() use ($new_user) {
            return factory(\App\Models\Article::class)->create(['user_id' => $new_user->id])->id;
        },
        'tag_id' => function() {
            return factory(\App\Models\Tag::class)->create()->id;
        }
    ];
});
