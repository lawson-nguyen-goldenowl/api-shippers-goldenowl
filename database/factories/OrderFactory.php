<?php

use App\statusOrder as Status;
use App\orders as Orders;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Http;

$factory->define(Orders::class, function (Faker $faker) {
    $statusNew = Status::where('title', 'new')->first()->id;
    // $recipient = getInfoCustomer();
    // $shipper = App\shipper::all()->random()->id;
    $generateAdd = generateLocationAroundCity();
    $district = App\districts::where('name','like' ,"%{$generateAdd['district']}%")->first();
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


function getInfoCustomer()
{
    $info = Http::get("https://api.namefake.com/vietnamese-vietnam")->json();
    return [
        'name' => $info['name'],
        'address' => $info['address'],
        'phone' => $info['phone_h']
    ];
}

function generateLocationAroundCity()
{
    $respond['formatted_address'] = '';
    while (!$respond['formatted_address']) {
        $lng = '106.6' . rand(11111, 99999);
        $lat = '10.7' . rand(11111, 99999);
        $latlng = $lat . ',' . $lng;
        $url = "https://rsapi.goong.io/Geocode?latlng=" . $latlng . "&api_key=fmEgMPeCgZ6Ap7eiQm5C9dKTzlz2OyGIF4zg3cIy";
        $respond = Http::get($url)->json()['results'][0];
    }
    $fullAddress = $respond['name'] . ' ' . $respond['formatted_address'];
    $address_component = $respond['address_components'];
    $district = $address_component[count($address_component) - 2]['short_name'];
    $location = $respond['geometry']['location']['lat'].','.$respond['geometry']['location']['lng'];
    if (!$district) {
        $address = str_replace(', Hồ Chí Minh', '', $respond['formatted_address']);
        $district = trim(substr($address, strrpos($address, ',') + 1));
    }
    return [
        'fullAddress' => $fullAddress,
        'district' => $district,
        'location' => $location
    ];
}
