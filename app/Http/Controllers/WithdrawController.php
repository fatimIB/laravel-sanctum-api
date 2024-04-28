<?php

namespace App\Http\Controllers;

use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $withdraws = Withdraw::all();
        return response()->json($withdraws);
    }

    public function show($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        return response()->json($withdraw);
    }

    public function updateWithdraw(Request $request, $id)
    {
        $withdraw = Withdraw::findOrFail($id);

        $request->validate([
            'method' => 'required|string',
            'points' => 'required|integer|min:10',
            'status' => 'required|string|in:pending,paid,rejected'
        ]);

        $withdraw->update([
            'method' => $request->method,
            'points' => $request->points,
            'status' => $request->status
        ]);

        return response()->json($withdraw);
    }

    public function newWithdraw(Request $request)
    {
        $request->validate([
            'method' => 'required|string',
            'points' => 'required|integer|min:10'
        ]);

        $withdraw = Withdraw::create([
            'method' => $request->method,
            'points' => $request->points,
            'status' => 'pending' // Par défaut, le statut est "pending" pour une nouvelle demande
        ]);

        return response()->json($withdraw, 201);
    }

    public function deleteWithdraw($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $withdraw->delete();
        return response()->json(['message' => 'Retrait supprimé avec succès']);
    }

    public function balanceWithdraw(Request $request)
    {
        // Logique pour vérifier l'équilibre des retraits
    }

    public function userBalanceWithdraw(Request $request)
    {
        // Logique pour créer un retrait en fonction de l'utilisateur et de l'équilibre
    }

    public function userPointsWithdraw(Request $request)
    {
        // Logique pour créer un retrait en fonction de l'utilisateur et des points
    }

    public function userWithdraws()
    {
        // Logique pour récupérer les retraits de l'utilisateur actuel
    }
}
