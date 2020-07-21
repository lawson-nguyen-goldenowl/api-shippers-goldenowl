<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('ordersnotdistributed', function () {
    $orders = App\orders::where('status', 1)->orderBy('idDistrict')->get();
    echo count($orders);
    return $orders;
});

Route::get('shippersnotdistributed', function () {
    return $shippers = App\shipper::with('works')->has('orders', '>', 0)->withCount('orders')->orderBy('orders_count')->first();

    $shippers = App\shipper::with('works')->has('orders', '>', 0)->withCount('orders')->orderBy('orders_count')->get();
    echo count($shippers);
    return $shippers;
});

Route::get('distribute_orders', 'api\orderController@distribute_orders');



Route::get('distribute', function () {
    // Get orders and shippers
    $orders = App\orders::where('status', 1)->orderBy('idDistrict')->get();
    $orders = sortOrders($orders);
    $limitOrder = 5;
    foreach ($orders as $key => $order) {
        $counter = count($order);
        while ($counter) {
            $shipper = App\shipper::has('orders', '<', 5)->withCount('orders')->district($key)->orderByDesc('orders_count')->first();
            if (!$shipper) continue 2;
            $lengthOrderSplice = $limitOrder - $shipper->orders_count;
            $ordersUpdate = array_splice($order, 0, $lengthOrderSplice);
            App\orders::whereIn('id', $ordersUpdate)->update([
                'idShipper' => $shipper->id,
                'status' => 2,
            ]);
            $counter = count($ordersUpdate);
        }
    }
    echo 'Allright';
});

function sortOrders($orders)
{
    $sortedOrders = [];
    $district = null;
    $lengthOrders = count($orders);
    $locations[0]['location'] = "10.7857313156128,106.667335510254";

    foreach ($orders as $key => $order) {
        if ($district != $order->idDistrict) {
            $lengthLocations = count($locations);
            if ($lengthLocations > 1) {
                if ($lengthLocations != 2 && $key != $lengthOrders) {
                    $sortedOrders[$district] = [];

                    while ($lengthLocations > 1) {

                        $source = $locations[0]['location'];
                        $destinations = array_splice($locations, 1);
                        $keyNearest = findNearestLocation($source, $destinations);
                        $sortedOrders[$district][] = $destinations[$keyNearest]['idOrder'];
                        $locations = array();
                        $locations[0] = $destinations[$keyNearest];

                        foreach ($destinations as $key => $destination) {
                            if ($key != $keyNearest) {
                                $locations[] = $destination;
                            }
                        }
                        $lengthLocations = count($locations);
                    }
                }
                $locations = array();
                $locations[0]['location'] = "10.7857313156128,106.667335510254";
            }
            $district = $order->idDistrict;
        }

        $sortedOrders[$district][] = $order->id;
        $locations[] = [
            'idOrder' => $order->id,
            'location' => $order->location
        ];
    }

    return $sortedOrders;
}

function findNearestLocation($source, $destinations)
{
    $lengthDestinations = count($destinations);
    $geocode = '';
    $min = PHP_INT_MAX;
    $keyResult = null;

    foreach ($destinations as $key => $destination) {
        $geocode .= $destination['location'];
        if ($key != $lengthDestinations - 1) {
            $geocode .= '|';
        }
    }

    $distances = getDistance($source, $geocode);
    if ($distances) {
        foreach ($distances as $key => $distance) {
            $disTemp = $distance['distance']['value'];
            if ($disTemp < $min) {
                $min = $disTemp;
                $keyResult = $key;
            }
        }
    } else {
        return false;
    }
    return $keyResult;
}

function getDistance($locationA, $locations)
{
    $url = "https://rsapi.goong.io/DistanceMatrix?";
    $origin = "origins=" . $locationA;
    $destination = "&destinations=" . $locations;
    $apiKey = "&api_key=3Y56i5KzlZslLKvPNr6T9pm2WbMRYq1a9meZtj2M";
    $url .= $origin . $destination . $apiKey;
    $respond = Http::get($url)->json();
    return $respond['rows'][0]['elements'] ?? false;
}




// Route::get('/getDistrict', function () {
//     // id hcm = 79
//     // get all districts
//     $allXa = Http::get("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/json/xa.json")->json();
//     $allDistrict = Http::get("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/json/huyen.json")->json();
//     foreach ($allDistrict as $district) {
//         if ($district['tinh_id'] == 79) {
//             $id = DB::table('districts')->insertGetId([
//                 'name' => $district['name'],
//                 "created_at" =>  \Carbon\Carbon::now(),
//                 "updated_at" => \Carbon\Carbon::now(),
//             ]);
//             $huyen_id = $district['id'];
//             foreach ($allXa as $xa) {
//                 if ($xa['huyen_id'] == $huyen_id) {
//                     DB::table('subDistricts')->insert([
//                         'name' => $xa['name'],
//                         'idDistrict' => $id,
//                         "created_at" =>  \Carbon\Carbon::now(),
//                         "updated_at" => \Carbon\Carbon::now(),
//                     ]);     
//                 }
//             }            
//         };
//     }
// });