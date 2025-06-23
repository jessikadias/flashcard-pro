<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\CreateDeckRequest;

class UpdateDeckRequest extends CreateDeckRequest
{
    /**
     * Get the validation rules that apply to the request.
     * 
     * Inherits from CreateDeckRequest but makes all fields optional
     * using the 'sometimes' rule for partial updates.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        
        // Make all fields optional for updates using 'sometimes'
        $rules['name'] = 'sometimes|required|string|max:50';
        $rules['is_public'] = 'sometimes|boolean';
        $rules['cards'] = 'sometimes|array|max:100';
        
        return $rules;
    }

    /**
     * Get custom validation messages.
     * 
     * Inherits most messages from parent CreateDeckRequest,
     * only overrides messages that are different for updates.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = parent::messages();
        
        // Only override the message that's different for updates
        $messages['cards.max'] = 'Cannot update more than 100 cards at once.';
        
        return $messages;
    }

    /**
     * Get the validated data for updating the deck.
     * 
     * Inherits most behavior from parent but doesn't automatically
     * add user_id since we're updating an existing deck.
     *
     * @return array<string, mixed>
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();
        
        // Remove cards from deck data as they're handled separately
        unset($validated['cards']);
        
        return $validated;
    }
} 