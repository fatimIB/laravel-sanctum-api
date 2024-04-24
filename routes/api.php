<?php

use App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;

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

Route::post('/register', [Auth::class, 'register']);
Route::post('/login', [Auth::class, 'login']);


Route::post('/password/reset/request', [PasswordResetController::class, 'requestReset']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $authController = new Auth();
    return $authController->logout($request);
});
  
  
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
