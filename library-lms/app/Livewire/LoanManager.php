<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Loan;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- Import Auth

class LoanManager extends Component
{
    use WithPagination;

    // Properties for Issuance Form
    public $bookSearch = '';
    public $selectedBookId;
    public $memberId = ''; 
    public $dueDateDays = 14;

    // Properties for Loan List
    public $loanSearch = '';
    public $filterStatus = 'on_loan'; 

    // Real-time search for book selection
    public function updatedBookSearch($value)
    {
        $this->reset(['selectedBookId']);
    }

    // Set the selected book ID from the search dropdown
    public function selectBook($bookId)
    {
        $this->selectedBookId = $bookId;
        $this->bookSearch = Book::find($bookId)->title;
    }

    // Issue the loan
    public function issueLoan()
    {
        // AUTH CHECK: Only Admins can issue loans
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            session()->flash('error', 'Unauthorized access: Only Admins can issue loans.');
            return;
        }

        $this->validate([
            'selectedBookId' => 'required|exists:books,id',
            'memberId' => 'required|integer|exists:users,id', // <-- Ensure memberId is a valid User ID (integer)
            'dueDateDays' => 'required|integer|min:1|max:60',
        ]);

        $book = Book::find($this->selectedBookId);

        DB::transaction(function () use ($book) {
            if ($book->available_copies < 1) {
                session()->flash('error', 'Loan failed: No available copies of that book remain.');
                return;
            }

            $book->decrement('available_copies');

            Loan::create([
                'user_id' => $this->memberId, 
                'book_id' => $book->id,
                'issued_at' => Carbon::now(),
                'due_date' => Carbon::now()->addDays((int)$this->dueDateDays),
            ]);

            session()->flash('success', 'Book successfully issued to Member ID: ' . $this->memberId);
            $this->reset(['bookSearch', 'selectedBookId', 'memberId', 'dueDateDays']);
        });
    }

    // Return the loan
    public function returnLoan($loanId)
    {
        // AUTH CHECK: Only Admins can process returns
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            session()->flash('error', 'Unauthorized access: Only Admins can process returns.');
            return;
        }

        $loan = Loan::with('book')->find($loanId);

        if (!$loan || $loan->returned_at !== null) {
            session()->flash('error', 'Error: Loan not found or already returned.');
            return;
        }

        DB::transaction(function () use ($loan) {
            $loan->update(['returned_at' => Carbon::now()]);
            $loan->book->increment('available_copies');
            
            $fine = 0;
            if ($loan->due_date->isPast()) {
                $overdueDays = $loan->due_date->diffInDays(Carbon::now());
                $fine = $overdueDays * 0.50; 
            }

            $message = 'Book returned successfully.';
            if ($fine > 0) {
                 $message .= " Member owes a fine of \${$fine}.";
            }

            session()->flash('success', $message);
        });
    }

    // Render the view
    public function render()
    {
        // Get books for the search dropdown
        $availableBooks = Book::select('id', 'title', 'available_copies')
            ->where('available_copies', '>', 0)
            ->where('title', 'like', '%' . $this->bookSearch . '%')
            ->limit(5)
            ->get();

        // Base Loan Query
        $loansQuery = Loan::with('book');

        // If not Admin, filter loans to only show their own
        if (Auth::check() && !Auth::user()->isAdmin()) {
            $loansQuery->where('user_id', Auth::id());
        }

        // Apply search filter (if searching by title or user_id)
        if ($this->loanSearch) {
            $loansQuery->where(function($query) {
                $query->whereHas('book', function ($q) {
                    $q->where('title', 'like', '%' . $this->loanSearch . '%');
                })->orWhere('user_id', 'like', '%' . $this->loanSearch . '%');
            });
        }
        
        // Apply status filter
        if ($this->filterStatus === 'on_loan') {
            $loansQuery->whereNull('returned_at');
        } elseif ($this->filterStatus === 'returned') {
            $loansQuery->whereNotNull('returned_at');
        }

        $loans = $loansQuery->orderBy('issued_at', 'desc')->paginate(10, pageName: 'loanPage');

        return view('livewire.loan-manager', [
            'availableBooks' => $availableBooks,
            'loans' => $loans
        ])->layout('layouts.app');
    }
}