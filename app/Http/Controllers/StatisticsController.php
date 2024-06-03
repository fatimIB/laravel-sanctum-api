<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    public function productOfTheMonth()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
    
        $sales = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
        
        $productSales = [];
        foreach ($sales as $sale) {
            foreach (explode(',', $sale->products) as $productName) {
                if (!isset($productSales[$productName])) {
                    $productSales[$productName] = 0;
                }
                $productSales[$productName]++;
            }
        }
    
        arsort($productSales);
        $productOfTheMonthName = key($productSales);
    
        if ($productOfTheMonthName) {
            $product = Product::where('name', $productOfTheMonthName)->first();
            if ($product) {
                return response()->json($product, 200);
            }
        }
    
        return response()->json(['message' => 'No product of the month found'], 404);
    }

    public function totalSalesToday()
    {
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay = Carbon::now()->endOfDay();

        $totalSalesToday = Sale::whereBetween('created_at', [$startOfDay, $endOfDay])->count();

        return response()->json(['totalSalesToday' => $totalSalesToday], 200);
    }

    public function numberOfUsers()
    {
        $numberOfUsers = User::where('role', 'user')->count();

        return response()->json(['numberOfUsers' => $numberOfUsers], 200);
    }

    public function profitToday()
    {
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay = Carbon::now()->endOfDay();

        
        $totalSalesToday = Sale::whereBetween('created_at', [$startOfDay, $endOfDay])->sum('total_price');

        
        $totalCommissionToday = Sale::whereBetween('created_at', [$startOfDay, $endOfDay])->sum('commission');

        
        $profitToday = $totalSalesToday - $totalCommissionToday;

        return response()->json(['profitToday' => $profitToday], 200);
    }

    public function monthlySalesData()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();
        
        $sales = Sale::whereBetween('created_at', [$startOfYear, $endOfYear])
                     ->get()
                     ->groupBy(function ($date) {
                         return Carbon::parse($date->created_at)->format('F'); // Group by months
                     });
        
        $salesData = [];
        foreach ($sales as $month => $salesForMonth) {
            $totalSales = $salesForMonth->sum('total_price');
            $numberOfSales = $salesForMonth->count();
            $totalCommission = $salesForMonth->sum('commission');
            $profit = $totalSales - $totalCommission;

            $salesData[] = [
                'month' => $month,
                'totalSales' => $totalSales,
                'numberOfSales' => $numberOfSales,
                'profit' => $profit
            ];
        }

        return response()->json($salesData, 200);
    }
}
