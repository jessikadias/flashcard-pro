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
        Schema::create('shared_decks', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to users table (deck owner)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Foreign key to decks table
            $table->foreignId('deck_id')->constrained()->onDelete('cascade');
            
            // Foreign key to users table (recipient)
            $table->foreignId('shared_with_user_id')->constrained('users')->onDelete('cascade');
            
            $table->text('message')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('shared_with_user_id');
            $table->index('deck_id');
            
            // Prevent duplicate sharing
            $table->unique(['user_id', 'deck_id', 'shared_with_user_id'], 'unique_deck_sharing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_decks');
    }
}; 