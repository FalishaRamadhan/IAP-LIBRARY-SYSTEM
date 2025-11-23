<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

/*
| API Routes
|
| These routes are typically authenticated using Laravel Sanctum for API tokens.
| The BookController (which you would need to create: php artisan make:controller Api/BookController)
| would handle requests and respond with JSON (using API Resources).
|
*/

// Public route to view the catalog
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);

// Protected routes (requires Sanctum token authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Librarian/Admin CRUD operations
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    // Example User/Member route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});