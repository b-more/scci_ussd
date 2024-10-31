<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UssdSessionController;

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

Route::middleware('api')->group(function () {
    // Test route
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });

    // USSD routes
    Route::post('/ussd/reapay', [UssdSessionController::class, 'reapay']);
});