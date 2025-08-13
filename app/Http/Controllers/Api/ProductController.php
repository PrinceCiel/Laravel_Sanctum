<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->get();
        $count = $products->count();
        if($count == 0){
            $res = [
                'success' => true,
                'message' => 'Data Not Found'
            ];
            return response()->json($res, 404);
        } else {
            $res = [
                'success' => true,
                'data' => $products,
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
            'name' => 'required|string|max:155|unique:products',
            'desc' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'id_category' => 'required',
            'foto' => 'required|image'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = new Product;
        $product->name = $request->name;
        $product->desc = $request->desc;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->id_category = $request->id_category;
        if($request->hasFile('foto')){
            $path = $request->file('foto')->store('products', 'public');
            $product->foto = $path;
        }
        $product->save();
        $res = [
            'success' => true,
            'data' => $product,
            'message' => 'Created Successfully'
        ];
        return response()->json($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);
        if(! $product){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Data Found'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:155|unique:products,id,' . $id,
            'desc' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'id_category' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::find($id);
        $product->name = $request->name;
        $product->desc = $request->desc;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->id_category = $request->id_category;
        if($request->hasFile('foto')){
            if($product->foto && Storage::disk('public')->exists($product->foto)){
                Storage::disk('public')->delete($product->foto);
            }
            $path = $request->file('foto')->store('products', 'public');
            $product->foto = $path;
        }
        $product->save();
        $res = [
            'success' => true,
            'data' => $product,
            'message' => 'Updated Successfully'
        ];
        return response()->json($res, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(! $product){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ], 200);
    }
}
