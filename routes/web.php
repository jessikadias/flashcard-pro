<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeckController;
use App\Livewire\DeckList;

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
    Route::get('/decks/create', [DeckController::class, 'create'])->name('decks.create');
    Route::get('/decks/{deck}', [DeckController::class, 'show'])->name('decks.show');
    Route::get('/decks/{deck}/edit', [DeckController::class, 'edit'])->name('decks.edit');
});

require __DIR__.'/auth.php';
