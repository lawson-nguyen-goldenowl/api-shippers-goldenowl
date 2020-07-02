<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\orders as Order;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\permission as Permission;

class orderController extends Controller
{
    //
    public function all(Request $request){

    }
    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user->permission != Permission::where('title', 'admin')->first()->id) {
            return response()->json([
                'error' => 'Forbidden'
            ], 403);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:orders',
                'weight' => 'required|numeric',
                'recipientName' => 'required',
                'recipientPhone' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->errors()
                ],
                400
            );
        }

        $order = new Order;
        $order->name = $request->name;
        $order->weight = $request->weight;
        $order->recipientName = $request->recipientName;
        $order->recipientPhone = $request->recipientPhone;
        $saved = $order->save();
        if (!$saved) {
            return response()->json([
                'error' => 'INTERNAL SERVER ERROR'
            ], 500);
        }
        return response()->json([
            'success' => [
                'order' => $order
            ]
        ], 200);
    }
}
