<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    /**
     * Display a listing of the resource (Public catalog view).
     */
    public function index()
    {
        // Fetches a list of all books, paginated for performance.
        $books = Book::paginate(10);
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage (Admin/Librarian only).
     */
    public function store(Request $request)
    {
        // NOTE: In a real app, use Gate::authorize('create', Book::class) here.
        // Assuming authorization passes for this example.
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'total_copies' => 'required|integer|min:1',
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'total_copies' => $validated['total_copies'],
            'available_copies' => $validated['total_copies'], // Available = Total upon creation
        ]);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201); // 201 Created is standard for successful POST requests
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage (Admin/Librarian only).
     */
    public function update(Request $request, Book $book)
    {
        // NOTE: In a real app, use Gate::authorize('update', $book) here.

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            // Ignore current book ID for unique check
            'isbn' => 'sometimes|string|unique:books,isbn,' . $book->id,
            'total_copies' => 'sometimes|integer|min:1',
        ]);

        // Logic to prevent reducing total copies below the number currently on loan
        if (isset($validated['total_copies'])) {
            $currentLoans = $book->total_copies - $book->available_copies;
            if ($validated['total_copies'] < $currentLoans) {
                return response()->json([
                    'message' => 'Cannot reduce copies below the number currently on loan (' . $currentLoans . ' needed).'
                ], 422); // 422 Unprocessable Entity
            }
            // Recalculate available copies based on the new total and existing loans
            $validated['available_copies'] = $validated['total_copies'] - $currentLoans;
        }
        
        $book->update($validated);
        
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage (Admin/Librarian only).
     */
    public function destroy(Book $book)
    {
        // NOTE: In a real app, use Gate::authorize('delete', $book) here.

        // Check for outstanding loans
        if ($book->available_copies < $book->total_copies) {
             return response()->json([
                'message' => 'Cannot delete book: Outstanding copies are currently on loan.'
            ], 409); // 409 Conflict indicates resource conflict
        }

        $book->delete();

        return response()->json(null, 204); // 204 No Content is standard for successful deletion
    }
}