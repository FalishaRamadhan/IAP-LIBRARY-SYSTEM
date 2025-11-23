<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of all loans (Admin/Librarian only).
     * Accessible via: GET /api/loans
     */
    public function index()
    {
        // NOTE: Authorize this route (only Admin/Librarian should see all loans)
        // Fetches all loans and eager-loads the 'book' details for the resource.
        $loans = Loan::with('book')->orderBy('issued_at', 'desc')->paginate(10);
        return LoanResource::collection($loans);
    }

    /**
     * Issue a new loan (Member/Librarian can create).
     * Accessible via: POST /api/loans/issue
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // user_id is crucial: In a real app, this would come from the authenticated user token (Sanctum)
            'user_id' => 'required|exists:users,id', 
            'book_id' => 'required|exists:books,id',
            // Optional field: number of days until the book is due
            'due_days' => 'integer|min:1|max:30', 
        ]);

        $book = Book::find($validated['book_id']);

        // Check availability
        if ($book->available_copies < 1) {
            return response()->json([
                'message' => 'The requested book is currently out of stock.'
            ], 409); // 409 Conflict
        }

        // Decrement available copies
        $book->decrement('available_copies');

        // Create the loan record
        $loan = Loan::create([
            'user_id' => $validated['user_id'],
            'book_id' => $book->id,
            'issued_at' => Carbon::now(),
            'due_date' => Carbon::now()->addDays($validated['due_days'] ?? 14), // Default 14 days
        ]);

        // Return the newly created loan record with book details loaded
        return (new LoanResource($loan->load('book')))
            ->response()
            ->setStatusCode(201); // 201 Created
    }
    
    /**
     * Return a book (Mark a loan as returned).
     * Accessible via: POST /api/loans/{loan}/return
     */
    public function returnBook(Loan $loan)
    {
        // NOTE: Authorize this (Member/Librarian can return)

        if ($loan->returned_at !== null) {
            return response()->json(['message' => 'This book has already been returned.'], 400);
        }

        // Update the loan record with the return timestamp
        $loan->update(['returned_at' => Carbon::now()]);

        // Increment available copies on the book record
        $loan->book->increment('available_copies');

        // Logic for calculating fines would typically be added here if needed

        return new LoanResource($loan->load('book'));
    }
}