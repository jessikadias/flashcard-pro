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
                <div class="mt-6 sm:flex sm:flex-row-reverse">
                    <x-button wire:click="createDeck" size="md" class="w-full sm:w-auto sm:ml-3">
                        Create Deck
                    </x-button>
                    <x-button wire:click="close" variant="secondary" size="md" class="w-full sm:w-auto mt-3 sm:mt-0">
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>
