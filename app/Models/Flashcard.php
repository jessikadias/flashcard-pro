<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a flashcard in the application.
 */
class Flashcard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'question',
        'answer',
        'order',
        'deck_id',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * The deck that contains the flashcard.
     */
    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * Scope to get flashcards in order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    /**
     * Scope to get flashcards for a specific deck.
     */
    public function scopeForDeck($query, $deckId)
    {
        return $query->where('deck_id', $deckId);
    }

    /**
     * The next flashcard in the deck.
     */
    public function getNextAttribute(): ?self
    {
        return static::where('deck_id', $this->deck_id)
                    ->where('order', '>', $this->order ?? 0)
                    ->orderBy('order')
                    ->first();
    }

    /**
     * The previous flashcard in the deck.
     */
    public function getPreviousAttribute(): ?self
    {
        return static::where('deck_id', $this->deck_id)
                    ->where('order', '<', $this->order ?? 0)
                    ->orderByDesc('order')
                    ->first();
    }

    /**
     * Check if this is the first flashcard.
     */
    public function getIsFirstAttribute(): bool
    {
        return !static::where('deck_id', $this->deck_id)
                     ->where('order', '<', $this->order ?? 0)
                     ->exists();
    }

    /**
     * Check if this is the last flashcard.
     */
    public function getIsLastAttribute(): bool
    {
        return !static::where('deck_id', $this->deck_id)
                     ->where('order', '>', $this->order ?? 0)
                     ->exists();
    }

    /**
     * The position of the flashcard in the deck.
     */
    public function getPositionAttribute(): int
    {
        return static::where('deck_id', $this->deck_id)
                    ->where('order', '<=', $this->order ?? 0)
                    ->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flashcard) {
            if (is_null($flashcard->order)) {
                $maxOrder = static::where('deck_id', $flashcard->deck_id)->max('order');
                $flashcard->order = ($maxOrder ?? 0) + 1;
            }
        });
    }
} 