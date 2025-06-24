<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeckController;
use App\Livewire\DeckList;
use App\Livewire\DeckDetails;

// Welcome route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Deck routes
Route::middleware(['auth'])->group(function () {
    Route::get('/decks', DeckList::class)->name('decks.index');
    Route::get('/decks/{deck}/edit', DeckDetails::class)->name('decks.edit');
    Route::get('/decks/{deck}/flashcards/{flashcardId?}', \App\Livewire\FlashcardForm::class)->name('flashcards.edit');
    Route::get('/decks/{deck}/study', \App\Livewire\DeckStudy::class)->name('decks.study');
});

require __DIR__.'/auth.php';
