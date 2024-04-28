<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index()
    {
        // Logique pour récupérer toutes les balances
        $balances = Balance::all();
        return response()->json($balances);
    }

    public function userBalance()
    {
        // Logique pour récupérer la balance de l'utilisateur actuel
        // Vous devrez ajuster cette logique en fonction de votre système d'authentification
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }
        $balance = Balance::where('user_id', $user->id)->first();
        return response()->json($balance);
    }

    public function show($id)
    {
        // Logique pour récupérer une balance spécifique par son ID
        $balance = Balance::findOrFail($id);
        return response()->json($balance);
    }
}
