<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Define fillable fields for mass assignment safety
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'total_copies',
        'available_copies', // Included for loan management
    ];

    /**
     * Get the loans for the book.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}