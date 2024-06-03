<?php

use App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/statistics/product-of-the-month', [StatisticsController::class, 'productOfTheMonth']);
Route::get('/statistics/total-sales-today', [StatisticsController::class, 'totalSalesToday']);
Route::get('/statistics/number-of-users', [StatisticsController::class, 'numberOfUsers']);
Route::get('/statistics/profit-today', [StatisticsController::class, 'profitToday']);
Route::get('/statistics/monthly-sales-data', [StatisticsController::class, 'monthlySalesData']);

// User Routes
Route::post('user/register', [Auth::class, 'register']);
Route::post('user/login', [Auth::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/logout', [Auth::class, 'logout']);
    Route::put('user/profile', [Auth::class, 'updateProfile']);
    Route::get('user/profile', [Auth::class, 'getProfile']); 
    Route::delete('user/delete/{id}', [Auth::class, 'destroy']);
    Route::post('/check-password', [Auth::class, 'checkPassword']);
    Route::post('/user/change-password', [Auth::class, 'changePassword']);

});

// Admin Routes
Route::post('admin/register', [AdminController::class, 'register']);
Route::post('admin/login', [AdminController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    Route::post('admin/logout', [AdminController::class, 'logout']);
    Route::put('admin/update/{id}', [AdminController::class, 'update']);
    Route::delete('admin/delete/{id}', [AdminController::class, 'destroy']);
});


Route::post('/password/reset/request', [PasswordResetController::class, 'requestReset']);
Route::post('/password/reset/verify', [PasswordResetController::class, 'verifyCodeAndEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);


// Routes for admin
Route::group(['middleware' => 'admin'], function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

// Routes for users
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);


// Routes for admin POST
Route::group(['middleware' => 'admin'], function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

Route::get('/points/{userId}', [PointsController::class, 'getUserPoints']);
Route::get('/points', [PointsController::class, 'all']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::put('/points/{id}', [PointsController::class, 'updateStatus']);
});
Route::middleware('auth:sanctum')->get('/user/points', [PointsController::class, 'UserPoints']);

Route::get('/sales/total/{userId}', [SaleController::class, 'calculateTotalSales']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales/me', [SaleController::class, 'mySales']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('sales')->group(function () {
        Route::get('/', [SaleController::class, 'all']);
        Route::get('/{id}', [SaleController::class, 'single']);
        
        Route::middleware('admin')->group(function () {
            Route::post('/update/{id}', [SaleController::class, 'updateSale']);
            Route::post('/new', [SaleController::class, 'newSale']);
            Route::delete('/{id}', [SaleController::class, 'deleteSale']);
        });
    });
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/balance', [BalanceController::class, 'userBalance']);
    Route::get('/balance/{id}', [BalanceController::class, 'show']);
    Route::put('/balance/update/{userId}', [BalanceController::class, 'updateBalance']);
});

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::post('/withdraws/new', [WithdrawController::class, 'requestWithdraw']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/withdraws/userWithdrawals', [WithdrawController::class, 'getUserWithdrawals']);
});


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/withdraws/all', [WithdrawController::class, 'getAllWithdraws']);
    Route::put('/withdraws/{id}', [WithdrawController::class, 'updateWithdrawStatus']);
});

/*
Route::prefix('withdraws')->group(function () {
    Route::get('/', [WithdrawController::class, 'index']); // Endpoint pour récupérer tous les retraits
    Route::get('/me', [WithdrawController::class, 'userWithdraws']); // Endpoint pour récupérer les retraits de l'utilisateur actuel
    Route::get('/{id}', [WithdrawController::class, 'show']); // Endpoint pour récupérer un retrait par son ID
    Route::post('/update/{id}', [WithdrawController::class, 'updateWithdraw']); // Endpoint pour mettre à jour un retrait
    Route::post('/new', [WithdrawController::class, 'newWithdraw']); // Endpoint pour créer un nouveau retrait
    Route::post('/delete/{id}', [WithdrawController::class, 'deleteWithdraw']); // Endpoint pour supprimer un retrait
    Route::post('/balance', [WithdrawController::class, 'balanceWithdraw']); // Endpoint pour vérifier l'équilibre des retraits
    Route::post('/user-balance-withdraw', [WithdrawController::class, 'userBalanceWithdraw']); // Endpoint pour créer un retrait en fonction de l'utilisateur et de l'équilibre
    Route::post('/user-points-withdraw', [WithdrawController::class, 'userPointsWithdraw']); // Endpoint pour créer un retrait en fonction de l'utilisateur et des points
});
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
