<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\LoanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are typically authenticated using Laravel Sanctum for API tokens.
|
*/

// --- Public Access Routes ---
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);

// --- Protected Routes (Requires Sanctum token authentication) ---
Route::middleware('auth:sanctum')->group(function () {
    // User route for token verification
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // BOOK Management (Admin/Librarian CRUD)
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    
    // LOAN Management (Issue/Return)
    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loans/issue', [LoanController::class, 'store']); // Issue a new loan
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook']); // Mark a loan as returned
});