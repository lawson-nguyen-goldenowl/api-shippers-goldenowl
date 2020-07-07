<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\shipper::class, function (Faker $faker) {
    return [
        'id' => Str::random(8),
        'numberPlate' => $faker->postcode,
    ];
});