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
use App\Traits\Mapbox;
use Illuminate\Support\Facades\Http;

class orderController extends apiController
{
    use Mapbox;
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

    public function distribute()
    {
        $allOrders = Order::where('status', 1)->get();
        $shippers = shipper::with('works')->withCount('orders')->has('orders', '<', 1)->get();
        echo 'Number of orders : ' . count($allOrders) .'<br />';
        $counter = 0;
        
        foreach ($shippers as $v => $shipper) {

            echo '--------- Shipper :' . $shipper->id . '------  <br />';
            echo '<br />';
            echo 'List orders: <br/>';

            $source = $this->getSource($shipper);
            $collectOrders = $allOrders->whereIn('idDistrict', $shipper->work_locations);
            $locationOrders = array_chunk($collectOrders->pluck('location')->toArray(), 24);
            $coordinates = $source . implode(";", $locationOrders[0]);
            $graph = $this->createGraph($coordinates);
            $path = $this->findShortestPath($graph['distances']);
            $collectOrders = array_values($collectOrders->toArray());
            $idOrdersBeUpdate = [];
            
            foreach ($path as $index => $keyAllOrder) {
                if ( $index == 0 ) continue;
                $key = $keyAllOrder - 1;
                $idOrdersBeUpdate[] = $collectOrders[$key]['id'];
                echo $collectOrders[$key]['id'].' ';
                $counter++;
                $allOrders = $allOrders->where('id', '!=', $collectOrders[$key]['id']);
                echo count($allOrders);
                echo '<br/>';
                if ($index == (5 - $shipper->orders_count)) break;
            }

            Order::whereIn('id', $idOrdersBeUpdate)->update([
                'status' => 2,
                'idShipper' => $shipper->id
            ]);
            if (count($allOrders) < 1) break;
        }

        echo 'Number of distributed orders : ' . $counter;
    }

    public function getSource($shipper)
    {
        $wareHouse = "106.6673794,10.7857855";
        $source = $shipper['orders_count'] ? $shipper->orders->last()->location : $wareHouse;
        return $source . ";";
    }

    public function createGraph($coordinates)
    {
        return $this->directMatrix($coordinates);
    }

    public function findShortestPath($graph)
    {
        $path = [0];
        $pathLength = 1; // 1 
        $graphLength = count($graph); // 4

        while ($pathLength < $graphLength) {
            $tempIndex = $path[$pathLength - 1]; // 0, 2
            $tempArr = $graph[$tempIndex]; // [0 | 7778,7 | 6916 | 10295,8] , 
            $minIndex = $this->findMinKey($tempArr, $path); // 2
            $path[] = $minIndex; // [0, 2]
            $pathLength++; // 2 
        }
        return $path;
    }

    public function findMinKey($arr, $exceptKeys)
    {
        $min = PHP_FLOAT_MAX;
        $keyResult = null;
        foreach ($arr as $key => $value) {
            if ($value < $min && !in_array($key, $exceptKeys)) {
                $min = $value;
                $keyResult = $key;
            }
        }
        return $keyResult;
    }
}
