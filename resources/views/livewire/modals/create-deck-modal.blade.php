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
