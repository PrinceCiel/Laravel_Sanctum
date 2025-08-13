<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::where('id_user', Auth::id())->get();
        $count = $carts->count();
        if(! $carts){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        $res = [
            'success' => true,
            'data' => $carts,
            'message' => $count . 'Result Found',
        ];
        return response()->json($res, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|integer|not_in:0',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cart = new Cart;
        $cart->id_user = Auth::id();
        $cart->id_product = $request->id_product;
        $cart->qty = $cart->qty;
        $cart->save();
        $res = [
            'success' => true,
            'data' => $cart,
            'message' => 'Added to Cart',
        ];
        return response()->json($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cart = Cart::find($id);
        if(! $cart){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $cart,
            'message' => 'Data Found'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|integer|not_in:0',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cart = Cart::find($id);
        $cart->id_user = Auth::id();
        $cart->id_product = $request->id_product;
        $cart->qty = $cart->qty;
        $cart->save();
        $res = [
            'success' => true,
            'data' => $cart,
            'message' => 'Updated Successfully',
        ];
        return response()->json($res, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cart = Cart::find($id);
        if(! $cart){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        $cart->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ], 200);
    }
}
