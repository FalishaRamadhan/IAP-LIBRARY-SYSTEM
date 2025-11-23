<?php

namespace App\Livewire;

use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;

class BookManager extends Component
{
    use WithPagination;

    // Public properties that are automatically synced with the Blade view
    public $title = '';
    public $author = '';
    public $isbn = '';
    public $copies = 1;
    public $editingBookId = null;
    public $search = '';

    // Validation rules
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            // Unique ISBN validation is critical, ignore current book's ID if updating
            'isbn' => 'required|string|max:50|unique:books,isbn,' . $this->editingBookId,
            'copies' => 'required|integer|min:1',
        ];
    }

    // Runs every time the 'search' property changes
    public function updatedSearch()
    {
        $this->resetPage(); // Reset pagination when search query changes
    }

    // Function to reset all public properties (form clearing)
    public function resetForm()
    {
        $this->title = '';
        $this->author = '';
        $this->isbn = '';
        $this->copies = 1;
        $this->editingBookId = null;
    }

    // Save or Update a book record
    public function save()
    {
        $validatedData = $this->validate();

        if ($this->editingBookId) {
            // Update mode
            $book = Book::find($this->editingBookId);
            $book->update([
                'title' => $validatedData['title'],
                'author' => $validatedData['author'],
                'isbn' => $validatedData['isbn'],
                'total_copies' => $validatedData['copies'],
                // Simple logic: assume available copies match total for simplicity in this example
                'available_copies' => $validatedData['copies'],
            ]);
            session()->flash('success', 'Book updated successfully!');

        } else {
            // Create mode
            Book::create([
                'title' => $validatedData['title'],
                'author' => $validatedData['author'],
                'isbn' => $validatedData['isbn'],
                'total_copies' => $validatedData['copies'],
                'available_copies' => $validatedData['copies'], // Same as total on creation
            ]);
            session()->flash('success', 'New book added to library!');
        }

        $this->resetForm();
    }

    // Populate the form for editing
    public function edit(Book $book)
    {
        $this->editingBookId = $book->id;
        $this->title = $book->title;
        $this->author = $book->author;
        $this->isbn = $book->isbn;
        $this->copies = $book->total_copies;
    }

    // Delete a book record
    public function delete($bookId)
    {
        Book::find($bookId)->delete();
        session()->flash('error', 'Book deleted successfully.');
    }

    // Renders the component view, fetching necessary data
    public function render()
    {
        // Query the database with search and paginate the results
        $books = Book::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('author', 'like', '%' . $this->search . '%')
            ->orWhere('isbn', 'like', '%' . $this->search . '%')
            ->paginate(10); // Paginate results

        return view('livewire.book-manager', [
            'books' => $books
        ])->layout('layouts.app');
    }
}