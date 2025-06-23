<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ListDecksRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:name,created_at,updated_at',
            'order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
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
            'search.max' => 'Search term cannot exceed 255 characters.',
            'sort.in' => 'Sort field must be one of: name, created_at, updated_at.',
            'order.in' => 'Order must be either asc or desc.',
            'per_page.integer' => 'Per page must be a number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
        ];
    }

    /**
     * Get the validated search term.
     */
    public function getSearch(): ?string
    {
        return $this->validated('search');
    }

    /**
     * Get the validated sort field.
     */
    public function getSort(): string
    {
        return $this->validated('sort', 'created_at');
    }

    /**
     * Get the validated order direction.
     */
    public function getOrder(): string
    {
        return $this->validated('order', 'desc');
    }

    /**
     * Get the validated per page value.
     */
    public function getPerPage(): int
    {
        return $this->validated('per_page', 15);
    }
} 