<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            
            // Assuming we will have a 'users' table for members/librarians
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Link to the books table
            $table->foreignId('book_id')->constrained()->onDelete('restrict');
            
            $table->timestamp('issued_at');
            $table->timestamp('due_date');
            $table->timestamp('returned_at')->nullable(); // Nullable until the book is returned
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};