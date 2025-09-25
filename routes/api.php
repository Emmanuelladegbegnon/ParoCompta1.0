<?php

use App\Http\Controllers\Api\ParishController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EntryController;
// use App\Http\Controllers\Api\PaymentController; // Disabled until Payment model exists
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

// API Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Routes pour les paroisses
    Route::apiResource('parishes', ParishController::class);
    
    // Routes pour les catégories
    Route::apiResource('parishes.categories', CategoryController::class)->shallow();
    Route::get('categories/{category}/fields', [CategoryController::class, 'fields']);
    
    // Routes pour les entrées
    Route::apiResource('entries', EntryController::class);
    Route::get('entries/{entry}/download', [EntryController::class, 'download']);
    
    // Routes pour les paiements (désactivées: modèle/migration Payment manquants)
    // Route::get('payments', [PaymentController::class, 'index']);
    // Route::post('payments', [PaymentController::class, 'store']);
    // Route::get('payments/stats', [PaymentController::class, 'stats']);
});