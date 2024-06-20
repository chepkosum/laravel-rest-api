<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){

        $products = Product::get();
        if($products->count() > 0){
            return ProductResource::collection($products);
        }
        else{
            return response()->json(['message' => 'No Products Available'], 200);
}
    }


    public function store(Request $request) {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0', // Ensuring price is a non-negative integer
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All Fields Are required',
                'errors' => $validator->messages(),
            ], 422);
        }

        // Attempt to create the product
        try {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            return response()->json([
                'message' => 'Product Created Successfully',
                'data' => new ProductResource($product),
            ], 201); // 201 status code for resource creation

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500); // 500 status code for server error
        }
    }


    public function show(Product $product){
        return new ProductResource($product);
    }



    public function update(Request $request, Product $product)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|integer|min:0', // Ensuring price is a non-negative integer
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'All fields are required',
            'errors' => $validator->messages(),
        ], 422);
    }

    // Attempt to update the product
    try {
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Product Updated Successfully',
            'data' => new ProductResource($product),
        ], 200); // 200 status code for success

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to update product',
            'error' => $e->getMessage(),
        ], 500); // 500 status code for server error
    }
}



public function destroy(Product $product)
{
    try {
        // Check if the product exists
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        // Delete the product
        $product->delete();

        return response()->json([
            'message' => 'Product Deleted Successfully',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to delete product',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    // public function destroy(Product $product){
    //     $product->delete();
    //     return response()->json([
    //         'message'=> 'Product Deleted Successfully',
    //     ],200);
    // }
}
