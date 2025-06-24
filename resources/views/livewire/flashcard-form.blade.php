<div>
    <div class="min-h-screen bg-primary-50 p-4 sm:p-6 md:p-8">
        <div class="max-w-xl mx-auto">
            <!-- Header: Back Arrow and Title -->
            <div class="flex items-center space-x-4 mb-8">
                <a href="{{ route('decks.edit', $deck) }}" class="text-gray-700 hover:text-gray-500 transition flex-shrink-0">
                    <x-icons.arrow-left class="w-8 h-8" />
                </a>
                @if($this->isOwner())
                    <h1 class="text-3xl text-gray-700">
                        {{ $flashcard ? 'Edit' : 'Create' }} Flashcard
                    </h1>
                @else
                    <h1 class="text-3xl text-gray-700">
                        View Flashcard
                    </h1>
                @endif
            </div>

            <!-- Flashcard Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 space-y-6">
                <div>
                    <label for="question" class="block text-lg font-medium text-gray-700">Question</label>
                    <textarea id="question" wire:model="question" rows="4" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500 text-lg" placeholder="Enter the question"></textarea>
                    @error('question') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="answer" class="block text-lg font-medium text-gray-700">Answer</label>
                    <textarea id="answer" wire:model="answer" rows="4" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500 text-lg" placeholder="Enter the answer"></textarea>
                    @error('answer') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                @if($this->isOwner())
                    <div class="flex justify-between items-center pt-4">
                        <div>
                            @if($flashcard && $this->isOwner())
                                <x-button wire:click="$dispatch('openDeleteFlashcardModal', { flashcardId: {{ $flashcard->id }} })" variant="secondary" size="md">
                                    <x-icons.trash class="w-5 h-5 mr-2" />
                                    Delete
                                </x-button>
                            @endif
                        </div>
                        <x-button wire:click="save" size="md" fontWeight="font-bold">
                            That's it!
                        </x-button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <livewire:modals.delete-flashcard-modal />
</div>
