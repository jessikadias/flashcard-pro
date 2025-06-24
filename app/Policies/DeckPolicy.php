<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\User;

class DeckPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Deck $deck): bool
    {
        // User can view deck if they own it, it's public, or it's shared with them
        return $this->isOwner($user, $deck) 
            || $deck->is_public 
            || $deck->sharedWithUsers()->where('shared_with_user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function edit(User $user, Deck $deck): bool
    {
        return $this->isOwner($user, $deck);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deck $deck): bool
    {
        return $this->isOwner($user, $deck);
    }

    /**
     * Check if the user is the owner of the deck.
     */
    protected function isOwner(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }
}
