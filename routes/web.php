<?php

use Illuminate\Support\Facades\DB;
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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('random', function () {
    $all = App\districts::all();
    $arr = $all->filter(function ($e){
        return strpos($e->name, 'Bình Chánh');
    })->toArray();
    dd($arr[array_keys($arr)[0]]['id']);
});


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