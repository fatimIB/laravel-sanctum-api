<?php

namespace App\Http\Controllers;

use App\Models\Points;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class PointsController extends Controller
{

    public function all()
    {
        $points = Points::all();
        return response()->json($points);
    }

    public function me()
    {
        $userId = auth()->user()->id;
        $userPoints = Points::where('user_id', $userId)->get();
        return response()->json($userPoints);
    }

    public function single($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $totalPoints = $this->calculateUserPoints($id);

        $userPoints = Points::where('user_id', $id)->get();
        return response()->json([
            'total_points' => $totalPoints,
            'points_history' => $userPoints
        ]);
    }


    public static function updatePoints($userId, $totalPrice)
    {
        // Calculate the number of points earned based on $50 increments
        $points = intval($totalPrice / 50) * 100;

        $userPoints = Points::where('user_id', $userId)->first();

        if ($userPoints) {
            // Update points for existing user
            $userPoints->amount += $points; // Add the new points to the existing points
            $userPoints->save();
        } else {
            // Create points for new user
            Points::create([
                'user_id' => $userId,
                'amount' => $points,
                'status' => 'active',
            ]);
        }
    }
    public function getUserPoints($userId)
{
    $userPoints = Points::where('user_id', $userId)->first();

    if (!$userPoints) {
        return response()->json(['amount' => 0]); // Return 0 points as response
    }

    return response()->json($userPoints);
}
}
