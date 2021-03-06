<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\apiController;
use Illuminate\Http\Request;
use App\orders as Order;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\permission as Permission;
use App\statusOrder;

class orderController extends apiController
{
    //
    public function all()
    {
        $user = Auth::user();
        $data = [];
        if ($user->permission == 'shipper') {
            $data = Order::where('idShipper', $user->shipper->id)->get();
        }
        if ($user->permission == 'admin') {
            $data = Order::all();
        }        

        return $this->respond($data);

    }
    public function show($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'error' => "BAD REQUEST"
            ], 400);
        }
        $order->status = statusOrder::find($order->status)->title;
        return response()->json(['data' => $order], 200);
    }

    public function create(Request $request)
    {
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

    public function update(Request $request, $id)
    {
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
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'error' => "BAD REQUEST"
            ], 400);
        }
        $order->update($request->all());

        return response()->json(['success'], 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'error' => "BAD REQUEST"
            ], 400);
        }
        $order->delete();
        return response()->json(['success'], 200);
    }
}
