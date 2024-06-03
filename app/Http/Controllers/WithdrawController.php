<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balance;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
{
    public function requestWithdraw(Request $request)
    {
        // Validate the request data
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:bank_transfer,paypal', // Add more payment methods as needed
        ]);

        // Get the authenticated user's ID
        $userId = Auth::id();

        // Create a withdrawal request
        $withdraw = new Withdraw([
            'user_id' => $userId,
            'amount' => $request->amount,
            'method' => $request->method,
            'status' => 'pending', // Default status is pending
        ]);
        $withdraw->save();

        return response()->json(['message' => 'Withdrawal request submitted successfully']);
    }

    public function getAllWithdraws()
    {
        $withdraws = Withdraw::with('user')->get();
        return response()->json($withdraws);
    }

    public function updateWithdrawStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,paid,rejected']);
    
        $withdraw = Withdraw::findOrFail($id);
        $oldStatus = $withdraw->status;
        $withdraw->status = $request->status;
        $withdraw->save();
    
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            // Deduct the amount from the user's balance if the status is changed to 'paid'
            $balance = Balance::where('user_id', $withdraw->user_id)->first();
    
            if (!$balance) {
                return response()->json(['error' => 'User balance not found'], 404);
            }
    
            // Update the user's balance
            $balance->amount -= $withdraw->amount;
            $balance->save();
        }
    
        return response()->json(['status' => $withdraw->status]);
    }
    public function getUserWithdrawals()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Retrieve all withdrawals made by the authenticated user
        $withdrawals = Withdraw::where('user_id', $userId)->get();

        return response()->json($withdrawals);
    }

}
