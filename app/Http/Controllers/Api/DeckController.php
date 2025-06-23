<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeckResource;
use App\Http\Requests\Api\ListDecksRequest;
use App\Http\Requests\Api\CreateDeckRequest;
use App\Http\Requests\Api\UpdateDeckRequest;
use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DeckController extends Controller
{
    /**
     * Get all user decks.
     * 
     * @param ListDecksRequest $request
     * @return JsonResponse
     * 
     * @apiParam {String} [search] Search term to filter decks by name
     * @apiParam {String="name","created_at","updated_at"} [sort=created_at] Field to sort by
     * @apiParam {String="asc","desc"} [order=desc] Sort order
     * @apiParam {Number{1-100}} [per_page=15] Number of items per page
     */
    public function list(ListDecksRequest $request): JsonResponse
    {
        try {
            // Limit query to user's accessible decks
            $query = $request->user()->accessibleDecks()->with('flashcards');

            // Search functionality
            if ($search = $request->getSearch()) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }

            // Ordering
            $query->orderBy($request->getSort(), $request->getOrder());

            // Pagination
            $decks = $query->paginate($request->getPerPage());

            return response()->json([
                'data' => DeckResource::collection($decks),
                'meta' => [
                    'current_page' => $decks->currentPage(),
                    'last_page' => $decks->lastPage(),
                    'per_page' => $decks->perPage(),
                    'total' => $decks->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred while fetching user decks',
            ], 500);
        }
    }

    /**
     * Create a new deck.
     * 
     * @param CreateDeckRequest $request
     * @return JsonResponse
     * 
     * @apiParam {String} name Deck name (required, max 50 characters)
     * @apiParam {Boolean} [is_public=false] Whether the deck is public
     * @apiParam {Object[]} [cards] Array of initial flashcards (max 100)
     * @apiParam {String} cards.question Flashcard question (required, max 255 characters)
     * @apiParam {String} cards.answer Flashcard answer (required, max 255 characters)
     */
    public function create(CreateDeckRequest $request): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $cards = $request->getCards();

            $deck = Deck::create($validated);

            // Create flashcards if provided
            if (!empty($cards)) {
                $order = 1;
                foreach ($cards as $cardData) {
                    Flashcard::create([
                        'deck_id' => $deck->id,
                        'question' => $cardData['question'],
                        'answer' => $cardData['answer'],
                        'order' => $order++,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Deck created successfully',
                'data' => new DeckResource($deck),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred while creating the deck',
            ], 500);
        }
    }

    /**
     * Get flashcards from a specific deck.
     * 
     * @param Request $request
     * @param int|string $deckId The ID of the deck to retrieve
     * @return JsonResponse
     * 
     * @apiParam {Number} id Deck ID (URL parameter)
     */
    public function get(Request $request, $deckId): JsonResponse
    {
        try {
            $deck = $this->getDeckOrFail($request, $deckId);
            
            // Return early if getDeckOrFail returned a JsonResponse (404 error)
            if ($deck instanceof JsonResponse) {
                return $deck;
            }

            return response()->json([
                'data' => new DeckResource($deck),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred while fetching deck cards',
            ], 500);
        }
    }

    /**
     * Update a deck.
     * 
     * @param UpdateDeckRequest $request
     * @param int|string $deckId The ID of the deck to update
     * @return JsonResponse
     * 
     * @apiParam {Number} id Deck ID (URL parameter)
     * @apiParam {String} [name] Deck name (max 50 characters)
     * @apiParam {Boolean} [is_public] Whether the deck is public
     * @apiParam {Object[]} [cards] Array of flashcards to replace existing ones
     * @apiParam {String} cards.question Flashcard question (required, max 255 characters)
     * @apiParam {String} cards.answer Flashcard answer (required, max 255 characters)
     */
    public function update(UpdateDeckRequest $request, $deckId): JsonResponse
    {
        try {
            $deck = $this->getDeckOrFail($request, $deckId);
            
            // Return early if getDeckOrFail returned a JsonResponse (404 error)
            if ($deck instanceof JsonResponse) {
                return $deck;
            }

            $validated = $request->getValidatedData();
            $cards = $request->getCards();

            $deck->update($validated);

            // Update flashcards if provided
            if (!empty($cards)) {
                // Delete existing flashcards
                $deck->flashcards()->delete();
                
                // Create new flashcards
                $order = 1;
                foreach ($cards as $cardData) {
                    Flashcard::create([
                        'deck_id' => $deck->id,
                        'question' => $cardData['question'],
                        'answer' => $cardData['answer'],
                        'order' => $order++,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Deck updated successfully',
                'data' => new DeckResource($deck),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred while updating the deck.',
            ], 500);
        }
    }

    /**
     * Delete a deck or remove shared access.
     * 
     * @param Request $request
     * @param int|string $deckId The ID of the deck to delete
     * @return JsonResponse
     * 
     * @apiParam {Number} id Deck ID (URL parameter)
     */
    public function destroy(Request $request, $deckId): JsonResponse
    {
        try {
            $deck = $this->getDeckOrFail($request, $deckId);
            
            // Return early if getDeckOrFail returned a JsonResponse (404 error)
            if ($deck instanceof JsonResponse) {
                return $deck;
            }

            // Check if user is the owner
            if ($deck->canEdit($request->user())) {
                // User is owner - delete the entire deck
                $deck->delete();
                $message = 'Deck deleted successfully';
            } else {
                // User is not owner - remove shared relationship
                $request->user()->sharedDecks()->detach($deck->id);
                $message = 'Deck removed from your collection successfully';
            }

            return response()->json([
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred while deleting the deck',
            ], 500);
        }
    }

    /**
     * Get deck or return 404 JSON response if not found/accessible.
     * 
     * @param Request $request
     * @param int|string $deckId
     * @return Deck|JsonResponse
     */
    private function getDeckOrFail(Request $request, $deckId)
    {
        $deck = $request->user()->accessibleDecks()->where('id', $deckId)->first();

        if (!$deck) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Deck not found or is not accessible to the user',
            ], 404);
        }

        return $deck;
    }
}
