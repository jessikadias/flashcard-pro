<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeckController;
use App\Http\Middleware\AuthenticateApiToken;
use Illuminate\Http\Request;

// =============================================================================
// ALL API ROUTES (Protected by Custom Authentication Middleware)
// =============================================================================

Route::middleware(AuthenticateApiToken::class)->group(function () {
    
    // API Status/Health Check
    Route::get('/status', function () {
        return response()->json([
            'status' => 'online',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'message' => 'API is operational',
        ]);
    });

    // User Decks API - CRUD operations for decks
    Route::get('/decks', [DeckController::class, 'list']); // Get all user decks
    Route::post('/decks', [DeckController::class, 'create']); // Create new deck
    Route::get('/decks/{deckId}', [DeckController::class, 'get']); // Get specific deck
    Route::put('/decks/{deckId}', [DeckController::class, 'update']); // Update deck
    Route::delete('/decks/{deckId}', [DeckController::class, 'destroy']); // Delete deck

    // User info
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'created_at' => $request->user()->created_at?->toISOString(),
            ]
        ]);
    });
   
}); 