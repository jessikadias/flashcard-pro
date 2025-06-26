<div>
    @if ($show)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-30 px-4" wire:click.self="close">
            <div wire:click.stop class="relative bg-white rounded-lg p-6 shadow-xl w-full max-w-md">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Create New Deck
                </h3>

                <div>
                    <label for="deckName" class="block text-sm font-medium text-gray-700 mb-2">
                        Deck Name
                    </label>
                    <input
                        type="text"
                        id="deckName"
                        wire:model="deckName"
                        wire:keydown.enter="createDeck"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Enter deck name"
                        autofocus
                    />
                    @error('deckName') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Deck Visibility -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deck Visibility</label>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">
                                {{ $isPublic ? 'Public - Can be discovered by other users' : 'Private - Only visible to you and people you share it with' }}
                            </p>
                        </div>
                        <div class="flex items-center ml-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       wire:model.live="isPublic"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900">
                                    {{ $isPublic ? 'Public' : 'Private' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                @if($isPublic)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-blue-800 font-medium">Public URL</p>
                                <p class="text-xs text-blue-700 mt-1">After creating this deck, it will be accessible at a public URL that anyone can use to study.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($this->isAIAvailable())
                <!-- AI Generation Section -->
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-medium text-gray-700">
                            Generate with AI
                        </label>
                        <div class="flex items-center">
                            <input type="checkbox"
                                   wire:model.live="useAI"
                                   id="useAI"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="useAI" class="ml-2 text-sm text-gray-600">
                                Use AI to create flashcards
                            </label>
                        </div>
                    </div>

                    @if($useAI)
                    <div class="space-y-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div>
                            <label for="aiTheme" class="block text-sm font-medium text-gray-700 mb-2">
                                Topic/Theme
                            </label>
                            <input
                                type="text"
                                id="aiTheme"
                                wire:model.live="aiTheme"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                placeholder="e.g., Spanish vocabulary, Chemistry basics, History of Rome"
                            />
                            @error('aiTheme') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="aiDifficulty" class="block text-sm font-medium text-gray-700 mb-2">
                                Difficulty Level
                            </label>
                            <select
                                id="aiDifficulty"
                                wire:model="aiDifficulty"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                @foreach($aiDifficultyOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-xs text-blue-600 bg-blue-100 p-2 rounded">
                            <p class="font-medium">ℹ️ AI will generate ~10 flashcards for you!</p>
                            <p>You can edit or add more cards after creation.</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <div class="mt-6 sm:flex sm:flex-row-reverse">
                    <x-button
                        wire:click="createDeck"
                        size="md"
                        class="w-full sm:w-auto sm:ml-3"
                        :disabled="$isCreating"
                        wire:loading.attr="disabled"
                        wire:target="createDeck"
                    >
                        <span wire:loading.remove wire:target="createDeck" class="flex items-center justify-center">
                            Create Deck
                        </span>
                        <span wire:loading.flex wire:target="createDeck" class="flex items-center justify-center">
                            <x-icons.spinner class="mr-2 text-white animate-spin" />
                            @if($useAI && $aiTheme)
                                Generating...
                            @else
                                Creating...
                            @endif
                        </span>
                    </x-button>
                    <x-button
                        wire:click="close"
                        variant="secondary"
                        size="md"
                        class="w-full sm:w-auto mt-3 sm:mt-0"
                        :disabled="$isCreating"
                    >
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>
