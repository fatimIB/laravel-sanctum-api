<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\PointsController;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function all()
    {
        $sales = Sale::all();
        return response()->json($sales);
    }

    public function me()
    {
        $user = auth()->user();
        if ($user) {
            $sales = $user->sales;
            return response()->json($sales);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

    public function single($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            return response()->json($sale);
        } else {
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }

    public function updateSale(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'user_name' => 'required|string',
            'products.*.id' => 'required|exists:products,id',
            'total_price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::find($id);
        if ($sale) {
            $userId = $sale->user_id;
            $oldTotalPrice = $sale->total_price;

            // Fetch product names based on IDs
            $productNames = [];
            foreach ($validatedData['products'] as $product) {
                $productModel = Product::find($product['id']);
                if ($productModel) {
                    $productNames[] = $productModel->name;
                }
            }

            $sale->update([
                'user_id' => $validatedData['user_id'],
                'user_name' => $validatedData['user_name'],
                'products' => implode(',', $productNames),
                'total_price' => $validatedData['total_price'],
            ]);

            // Calculate difference in total price and update points accordingly
            $newTotalPrice = $validatedData['total_price'];
            $priceDifference = $newTotalPrice - $oldTotalPrice;
            PointsController::updatePoints($userId, $priceDifference);

            return response()->json(['message' => 'Sale updated successfully', 'sale' => $sale]);
        } else {
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }

    public function newSale(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'user_name' => 'required|string',
            'products.*.id' => 'required|exists:products,id',
            'total_price' => 'required|numeric|min:0',
        ]);

        // Fetch product names based on IDs
        $productNames = [];
        foreach ($validatedData['products'] as $product) {
            $productModel = Product::find($product['id']);
            if ($productModel) {
                $productNames[] = $productModel->name;
            }
        }

        // Create new sale record
        $sale = Sale::create([
            'user_id' => $validatedData['user_id'],
            'user_name' => $validatedData['user_name'],
            'products' => implode(',', $productNames),
            'total_price' => $validatedData['total_price'],
        ]);

        PointsController::updatePoints($sale->user_id, $sale->total_price);


        return response()->json(['message' => 'New sale created successfully', 'sale' => $sale], 201);
    }

    public function deleteSale($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $userId = $sale->user_id;
            $totalPrice = $sale->total_price;
            $sale->delete();

            // Subtract points earned from the deleted sale
            PointsController::updatePoints($userId, -$totalPrice);
            return response()->json(['message' => 'Sale deleted successfully']);
        } else {
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }

    public function calculateTotalSales($userId)
    {
        // Calculate total sales for the given user ID
        $totalSales = Sale::where('user_id', $userId)->sum('total_price');

        return $totalSales;
    }
}
