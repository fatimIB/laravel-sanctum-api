<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

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
        $sale = Sale::find($id);
        if ($sale) {
            $sale->update($request->all());
            return response()->json(['message' => 'Sale updated successfully']);
        } else {
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }

    public function newSale(Request $request)
    {
        $sale = Sale::create($request->all());
        return response()->json(['message' => 'New sale created successfully', 'sale' => $sale], 201);
    }

    public function deleteSale($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $sale->delete();
            return response()->json(['message' => 'Sale deleted successfully']);
        } else {
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }
}
