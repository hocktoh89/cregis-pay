<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Cregis API endpoints
Route::prefix('cregis')->group(function () {
    // Get supported coins
    Route::get('/coins', [\App\Http\Controllers\CregisController::class, 'getCoins']);
    
    // Create payout
    Route::post('/payout', [\App\Http\Controllers\CregisController::class, 'createPayout']);
    
    // Query payout status
    Route::get('/payout/{thirdPartyId}', [\App\Http\Controllers\CregisController::class, 'queryPayout']);
    
    // Webhook callback
    Route::post('/callback', [\App\Http\Controllers\WebhookController::class, 'handle']);
});

Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello World',
        'status' => 'success'
    ], 200);
});
