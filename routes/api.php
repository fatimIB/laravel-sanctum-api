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

// User Routes
Route::post('user/register', [Auth::class, 'register']);
Route::post('user/login', [Auth::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/logout', [Auth::class, 'logout']);
    Route::put('user/update/{id}', [Auth::class, 'update']);
    Route::delete('user/delete/{id}', [Auth::class, 'destroy']);
});

// Admin Routes
Route::post('admin/register', [AdminController::class, 'register']);
Route::post('admin/login', [AdminController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('admin/logout', [AdminController::class, 'logout']);
    Route::put('admin/update/{id}', [AdminController::class, 'update']);
    Route::delete('admin/delete/{id}', [AdminController::class, 'destroy']);
});


Route::post('/password/reset/request', [PasswordResetController::class, 'requestReset']);
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


Route::prefix('sales')->group(function () {
    Route::get('/', [SaleController::class, 'all']);
    Route::get('/me', [SaleController::class, 'me']);
    Route::get('/{id}', [SaleController::class, 'single']);
    Route::post('/update/{id}', [SaleController::class, 'updateSale']);
    Route::post('/new', [SaleController::class, 'newSale']);
    Route::post('/delete/{id}', [SaleController::class, 'deleteSale']);
});



Route::prefix('balance')->group(function () {
    Route::get('/', [BalanceController::class, 'index']); // Endpoint pour récupérer toutes les balances
    Route::get('/me', [BalanceController::class, 'userBalance']); // Endpoint pour récupérer la balance de l'utilisateur actuel
    Route::get('/{id}', [BalanceController::class, 'show']); // Endpoint pour récupérer une balance par son ID
});


Route::prefix('points')->group(function () {
    Route::get('/', [PointsController::class, 'all']);
    Route::get('/me', [PointsController::class, 'me']);
    Route::get('/{id}', [PointsController::class, 'single']);
});


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



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
