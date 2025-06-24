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
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to decks table
            $table->foreignId('deck_id')->constrained()->onDelete('cascade');
            
            $table->text('question');
            $table->text('answer');
            
            // Optional order for custom sequencing
            $table->integer('order')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('deck_id');
            $table->index(['deck_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
}; 