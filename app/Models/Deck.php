<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Deck Model
 * 
 * Represents a collection of flashcards that can be shared between users.
 */
class Deck extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_public',
        'user_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * The user that owns the deck.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The flashcards that are in the deck.
     */
    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class)->orderBy('order');
    }

    /**
     * The users that have access to the deck.
     */
    public function sharedWithUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shared_decks', 'deck_id', 'shared_with_user_id')
                    ->withPivot('message')
                    ->withTimestamps();
    }

    public function sharedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shared_decks', 'deck_id', 'user_id')
                    ->withPivot('message')
                    ->withTimestamps();
    }

    /**
     * The scope for public decks.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeAccessibleBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereIn('id', $user->sharedDecks()->pluck('decks.id'));
        });
    }

    /**
     * The number of flashcards in the deck.
     */
    public function getFlashcardCountAttribute(): int
    {
        return $this->flashcards()->count();
    }

    public function canEdit(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if the user can view the deck.
     */
    public function canView(User $user): bool
    {
        return $this->user_id === $user->id ||
               $this->is_public ||
               $this->sharedWithUsers()->where('shared_with_user_id', $user->id)->exists();
    }
} 