<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Orders::where('id_user', Auth::id())->latest()->get();
        $count = $orders->count();
        if($count == 0){
            $res = [
                'success' => true,
                'message' => 'Data Not Found'
            ];
            return response()->json($res, 404);
        } else {
            $res = [
                'success' => true,
                'data' => $orders,
                'message' => $count . ' Result Found',
            ];
            return response()->json($res, 200);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total' => 'required|integer',
            'qty' => 'required|integer',
            'price' => 'required|integer',
            'id_product' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $order = new Orders;
        $order->id_user = Auth::id();
        $order->order_code = 'ORD' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $order->total = $request->total;
        $order->save();

        $orderdetail = new OrderDetails;
        $orderdetail->id_order = $order->id;
        $orderdetail->id_product = $request->id_product;
        $orderdetail->qty = $request->qty;
        $orderdetail->price = $request->price;
        $orderdetail->save();
        $res = [
            'success' => true,
            'data' => $order,
            'message' => 'Created Successfully',
        ];
        return response()->json($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($code)
    {
        $order = Orders::where('order_code', $code)->first();
        $orderdetail = OrderDetails::find($order->id);
        if(! $order){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $orderdetail,
            'message' => 'Data Found'
        ], 200);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'total' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $order = Orders::find($id);
        $order->id_order = Auth::id();
        $order->order_code = $order->order_code;
        $order->total = $request->total;
        $order->save();
        $res = [
            'success' => true,
            'data' => $order,
            'message' => 'Updated Successfully',
        ];
        return response()->json($res, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Orders::find($id);
        if(! $order){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ], 200);
    }
}
