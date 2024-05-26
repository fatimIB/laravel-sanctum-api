<?php

namespace App\Http\Controllers;

use App\Models\Points;
use App\Models\User;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function all()
    {
        $points = Points::with('user')->get();
        return response()->json($points);
    }

    public function me()
    {
        $userId = auth()->user()->id;
        $userPoints = Points::where('user_id', $userId)->with('user')->get();
        return response()->json($userPoints);
    }

    public function single($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $totalPoints = $this->calculateUserPoints($id);

        $userPoints = Points::where('user_id', $id)->with('user')->get();
        return response()->json([
            'total_points' => $totalPoints,
            'points_history' => $userPoints
        ]);
    }

    public static function updatePoints($userId, $totalPrice)
    {
        $pointsEarned = intval($totalPrice / 50) * 100;
        $userPoints = Points::where('user_id', $userId)->orderBy('created_at', 'desc')->first();

        if ($userPoints) {
            $totalAmount = $userPoints->amount + $pointsEarned;
            if ($totalAmount > 5000) {
                $remainingPoints = $totalAmount - 5000;
                $userPoints->amount = 5000;
                $userPoints->save();

                Points::create([
                    'user_id' => $userId,
                    'amount' => $remainingPoints,
                    'status' => 'active',
                ]);
            } else {
                $userPoints->amount += $pointsEarned;
                $userPoints->save();
            }
        } else {
            Points::create([
                'user_id' => $userId,
                'amount' => $pointsEarned,
                'status' => 'active',
            ]);
        }
    }

    public function getUserPoints($userId)
    {
        $activePoints = Points::where('user_id', $userId)
            ->where('status', 'active')
            ->with('user')
            ->get();

        if ($activePoints->isEmpty()) {
            return response()->json([
                'total_points' => 0,
                'points_history' => [],
            ]);
        }

        $totalPoints = $activePoints->sum('amount');

        return response()->json([
            'total_points' => $totalPoints,
            'points_history' => $activePoints,
        ]);
    }


    public function updateStatus($id, Request $request)
    {
        $point = Points::find($id);
    
        if (!$point) {
            return response()->json(['error' => 'Point not found'], 404);
        }
    
        $point->status = $request->input('status', $point->status);
        $point->save();
    
        return response()->json(['status' => $point->status]);
    }
    
}
