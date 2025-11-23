<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BookResource; // Must import the BookResource

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // Include book details if the 'book' relationship was loaded
            // The API client will see the nested Book object here.
            'book_details' => new BookResource($this->whenLoaded('book')),
            'member_id' => $this->user_id, // Placeholder for user ID (from users table)
            'issued_at' => $this->issued_at->format('Y-m-d H:i:s'),
            'due_date' => $this->due_date->format('Y-m-d'),
            // Determine if the book has been returned
            'returned_at' => $this->returned_at ? $this->returned_at->format('Y-m-d H:i:s') : null,
            'is_returned' => $this->returned_at !== null,
        ];
    }
}