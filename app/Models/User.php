<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'onboarding_completed',
        'api_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
        ];
    }

    /**
     * The decks owned by the user.
     */
    public function decks(): HasMany
    {
        return $this->hasMany(Deck::class);
    }

    /**
     * The decks shared with the user.
     */
    public function sharedDecks(): BelongsToMany
    {
        return $this->belongsToMany(Deck::class, 'shared_decks', 'shared_with_user_id', 'deck_id')
                    ->withPivot('message')
                    ->withTimestamps();
    }

    /**
     * The decks the user has shared with others.
     */
    public function sharedByMe(): BelongsToMany
    {
        return $this->belongsToMany(Deck::class, 'shared_decks', 'user_id', 'deck_id')
                    ->withPivot('message')
                    ->withTimestamps();
    }

    /**
     * All decks the user can access.
     */
    public function accessibleDecks()
    {
        return Deck::where(function ($query) {
            $query->where('user_id', $this->id)
                  ->orWhereIn('id', $this->sharedDecks()->pluck('decks.id'));
        });
    }

    /**
     * The number of decks owned by the user.
     */
    public function getDeckCountAttribute(): int
    {
        return $this->decks()->count();
    }

    /**
     * The total number of flashcards across all user's decks.
     */
    public function getTotalFlashcardCountAttribute(): int
    {
        return $this->decks()->withCount('flashcards')->get()->sum('flashcards_count');
    }

    public function hasCompletedOnboarding(): bool
    {
        return (bool) $this->onboarding_completed;
    }

    /**
     * Generate a new API token for the user.
     */
    public function generateApiToken(): string
    {
        $this->api_token = Str::random(80);
        $this->save();

        return $this->api_token;
    }

    /**
     * Revoke the user's API token.
     */
    public function revokeApiToken(): void
    {
        $this->api_token = null;
        $this->save();
    }
}