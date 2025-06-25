<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\DeckList;
use App\Livewire\DeckDetails;

// Welcome route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/api-token', [ProfileController::class, 'generateApiToken'])->name('profile.api-token.generate');
    Route::delete('/profile/api-token', [ProfileController::class, 'revokeApiToken'])->name('profile.api-token.revoke');
});

// Deck routes
Route::middleware(['auth'])->group(function () {
    Route::get('/decks', DeckList::class)->name('decks.index');

    // Routes protected by deck policy
    Route::get('/decks/{deck}', DeckDetails::class)
        ->middleware('can:view,deck')
        ->name('decks.edit');
        
    Route::get('/decks/{deck}/flashcards/{flashcardId?}', \App\Livewire\FlashcardForm::class)
        ->middleware('can:view,deck')
        ->name('flashcards.edit');
        
    Route::get('/decks/{deck}/study', \App\Livewire\DeckStudy::class)
        ->middleware('can:view,deck')
        ->name('decks.study');
});

require __DIR__.'/auth.php';
