<?php

namespace App\Http\Controllers;

use App\Models\Points;
use App\Models\User;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function all()
    {
        // Récupérer tous les points
        $points = Points::all();
        return response()->json($points);
    }

    public function me()
    {
        // Récupérer les points de l'utilisateur actuel (vous devrez implémenter la logique pour obtenir l'ID de l'utilisateur actuel)
        $userId = auth()->user()->id; // Supposons que vous avez un système d'authentification en place
        $userPoints = Points::where('user_id', $userId)->get();
        return response()->json($userPoints);
    }

    public function single($id)
    {
        // Vérifier si l'utilisateur existe
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Récupérer les points de l'utilisateur par son ID
        $userPoints = Points::where('user_id', $id)->get();
        return response()->json($userPoints);
    }
}
