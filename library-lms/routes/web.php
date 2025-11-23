<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BookManager; 
use App\Livewire\LoanManager; 
use App\Livewire\UserManager;
use App\Livewire\Login; // <-- New
use Illuminate\Support\Facades\Auth;

// --- Public/Unauthenticated Routes ---
Route::get('/login', Login::class)->name('login');


// --- Protected Management Routes ---
// NOTE: We use a placeholder middleware 'auth' here. In a default Laravel install, 
// this protects routes if the user is not logged in, redirecting them to 'login'.
Route::middleware('auth')->group(function () {
    // Book Inventory Management (Accessible at the root URL)
    Route::get('/', BookManager::class)
        ->name('library.books');

    // Loan Issuance and Return Management
    Route::get('/loans', LoanManager::class)
        ->name('library.loans');

    // Member Registry Management
    Route::get('/members', UserManager::class)
        ->name('library.users');
});

// --- Logout Route (Standard Laravel) ---
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login')->with('success', 'You have been logged out.');
})->name('logout');