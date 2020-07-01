<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class orderController extends Controller
{
    //
    public function create(Request $request){
        return response()->json($request);
    }
}
