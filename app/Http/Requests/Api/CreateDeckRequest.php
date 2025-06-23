<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateDeckRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'is_public' => 'boolean',
            'cards' => 'array|max:100', // Limit number of cards that can be created at once
            'cards.*.question' => 'required|string|max:255',
            'cards.*.answer' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Deck name is required.',
            'name.max' => 'Deck name cannot exceed 50 characters.',
            'cards.max' => 'Cannot create more than 100 cards at once.',
            'cards.*.question.required' => 'Each card must have a question.',
            'cards.*.question.max' => 'Card question cannot exceed 255 characters.',
            'cards.*.answer.required' => 'Each card must have an answer.',
            'cards.*.answer.max' => 'Card answer cannot exceed 255 characters.',
        ];
    }

    /**
     * Get the validated data with defaults applied.
     *
     * @return array<string, mixed>
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();
        
        // Add user_id to the validated data
        $validated['user_id'] = $this->user()->id;
        
        return $validated;
    }

    /**
     * Get the validated cards data.
     *
     * @return array<int, array<string, string>>
     */
    public function getCards(): array
    {
        return $this->validated('cards', []);
    }
} 