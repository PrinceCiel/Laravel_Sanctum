<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $count = $categories->count();
        if($count == 0){
            $res = [
                'success' => true,
                'message' => 'Data Not Found'
            ];
            return response()->json($res, 404);
        } else {
            $res = [
                'success' => true,
                'data' => $categories,
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
            'name' => 'required|string|max:155|unique:categories',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = new Category;
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->save();

        $res = [
            'success' => true,
            'data' => $category,
            'message' => 'Created successfully',
        ];
        return response()->json($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);
        if(! $category){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Data Found'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:155|unique:categories,id,' . $id,
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = Category::find($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->save();

        $res = [
            'success' => true,
            'data' => $category,
            'message' => 'Updated successfully',
        ];
        return response()->json($res, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if(! $category){
            return response()->json([
                'message' => 'Data Not Found',
            ], 404);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ], 200);
    }
}
