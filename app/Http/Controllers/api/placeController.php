<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\api\apiController;
use App\places as Places;

class placeController extends apiController
{
    public function all() {
        $allPlaces = Places::all();
        return $this->respond($allPlaces);
    }
}
