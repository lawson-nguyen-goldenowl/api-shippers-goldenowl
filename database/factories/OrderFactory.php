<?php

use App\statusOrder as Status;
use App\orders as Orders;
use Faker\Generator as Faker;

$factory->define(Orders::class, function (Faker $faker) {
    $statusNew = Status::where('title', 'new')->first()->id;
    $shipper = App\shipper::all()->random()->id;
    return [
        'name' => $faker->sentence(6, true),
        'weight' => $faker->randomFloat(),
        'recipientName' => $faker->name,
        'recipientPhone' => $faker->phoneNumber,
        'recipientAddress' => $faker->address,
        'description' => $faker->text(150) ,
        'idShipper' => $shipper,
        'status' => $statusNew,
    ];
});
