<?php

use Carbon\Carbon;

/**
 * @return \App\Http\Router\Router
 */
function router() {
    /** @var \App\Http\Router\Router $router */
    $router = app('router');

    return $router;
}

/**
 * @return Faker\Generator
 */
function faker() {
    return app(Faker\Generator::class);
}

function stop($code, $array) {
    throw new \Symfony\Component\HttpKernel\Exception\HttpException($code, json_encode($array));
}
