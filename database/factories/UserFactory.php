<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\permission as Permission;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $infoUser = getInfoUser();
    $permissionShipper = Permission::where('title', 'shipper')->first()->id;
    return [
        'name' => $faker->firstName(),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('123456'), // password
        'remember_token' => Str::random(10),
        'permission' => $permissionShipper
    ];
});

function getInfoUser() {
    $info = Http::get("https://api.namefake.com/vietnamese-vietnam")->json();
    return [
        'name' => $info['name'],
        'address' => $info['address'],
        'phone' => $info['phone_h']
    ];
}