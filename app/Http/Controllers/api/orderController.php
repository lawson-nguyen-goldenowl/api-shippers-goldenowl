<?php

namespace App\Http\Controllers\api;

use App\districts;
use App\Http\Controllers\api\apiController;
use Illuminate\Http\Request;
use App\orders as Order;
use App\shipper;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\statusOrder;
use App\Traits\Dijkstra;
use Illuminate\Support\Facades\Http;

class orderController extends apiController
{
    use Dijkstra;
    //
    public function all(Request $request)
    {
        $user = Auth::user();
        $data = [];
        if ($user->permission == 'shipper') {
            $data = Order::where('idShipper', $user->shipper->id);
        }

        if ($user->permission == 'admin') {
            $validator = Validator::make(
                $request->all(),
                [
                    'district' => 'integer'
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
            $data = Order::query()->district($request)
                ->status($request);
        }

        $data = $data->get();
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

    public function distribute_orders()
    {
        return $orders = Order::where('status', 1)->orderBy('idDistrict')->get();
        $orders = sortOrders($orders);
    }

    public function createMatrixDistance($orders)
    {
        $locationKho = "10.7857313156128, 106.667335510254";
        $matrix = array();
        $length = count($orders);
        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < $length; $j++) {
                if ($i == $j) {
                    $matrix[$i][$i] = 0;
                } else if ($matrix[$j][$i]) {
                    $matrix[$i][$j] = $matrix[$j][$i];
                }
                $matrix[$i][$j] = $this->getDistance($orders[$i]->location, $orders[$j]->location);
            }
        }
    }

    public function getDistance($locationA, $locationB)
    {
        $url = "https://rsapi.goong.io/DistanceMatrix?";
        $origin = "origins=" . $locationA;
        $destination = "&destinations=" . $locationB;
        $apiKey = "&api_key=jaJ5xIhRGY2tfFxl17OP7rLmg878ZoyuyTjpfB5n";
        $url .= $origin . $destination . $apiKey;
        $respond = Http::get($url)->json();
        return $respond['rows'][0]['elements'][0]['distance'];
    }
}
