<?php

use App\statusOrder as Status;
use App\orders as Orders;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Http;

$factory->define(Orders::class, function (Faker $faker) {
    $statusNew = Status::where('title', 'new')->first()->id;
    $generateAdd = generateLocationAroundCity();
    $district = App\districts::where('name', 'like', "%{$generateAdd['district']}%")->first();
    return [
        'name' => $faker->sentence(6, true),
        'weight' => $faker->randomFloat(),
        'recipientName' => $faker->firstName(),
        'recipientPhone' => $faker->phoneNumber,
        'recipientAddress' => $generateAdd['fullAddress'],
        'description' => $faker->text(100),
        'idDistrict' => '',
        'status' => $statusNew,
        'idDistrict' => $district->id ?? 1,
        'location' => $generateAdd['location'],
    ];
});

function generateLocationAroundCity()
{
    $respond = [];
    while (empty($respond)) {
        $lng = '106.6' . rand(11111, 99999);
        $lat = '10.7' . rand(11111, 99999);
        $lnglat = $lng . ',' . $lat;
        $token = "pk.eyJ1IjoibGF3c29ubmd1eWVuIiwiYSI6ImNrY29vZ3p0bTBkb2oycG9iaWR0Z3BmaWEifQ.vxBH2svrPtPPi4uXrbLheA";
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . $lnglat . ".json?types=poi&access_token=". $token;
        $res = Http::get($url)->json();
        if ($res['features']) $respond = $res['features'][0];
    }

    $fullAddress = $respond['place_name'];
    $address_component = $respond['context'];
    $district = $address_component[count($address_component) - 3]['text'];
    $location = implode(',',$respond['geometry']['coordinates']);

    return [
        'fullAddress' => $fullAddress,
        'district' => $district,
        'location' => $location
    ];
}