<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Sale;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index()
    {
        // Retrieve all balances
        $balances = Balance::all();
        return response()->json($balances);
    }

    public function userBalance()
    {
        // Retrieve the balance of the current user
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $balance = Balance::where('user_id', $user->id)->first();
        return response()->json($balance);
    }

    public function show($id)
    {
        // Retrieve a specific balance by its ID
        $balance = Balance::findOrFail($id);
        return response()->json($balance);
    }

    public function updateBalance($userId)
    {
        // Calculate the total commission for the given user
        $totalCommission = Sale::where('user_id', $userId)->sum('commission');

        // Update or create the balance record for the user
        $balance = Balance::updateOrCreate(
            ['user_id' => $userId],
            ['amount' => $totalCommission, 'status' => 'active']
        );

        return response()->json($balance);
    }
}
