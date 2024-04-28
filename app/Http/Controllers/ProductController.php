<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function single($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($request->all());
            return response()->json(['message' => 'Product updated successfully']);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function newProduct(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json(['message' => 'Product created successfully', 'data' => $product], 201);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully']);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
