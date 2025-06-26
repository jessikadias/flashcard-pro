<div>
    <div class="min-h-screen bg-primary-50 p-4 sm:p-6 md:p-8">
        <div class="max-w-xl mx-auto">
            <!-- Header: Back Arrow and Title -->
            <div class="flex items-center space-x-4 mb-8">
                <a href="{{ route('decks.index') }}" class="text-gray-700 hover:text-gray-500 transition flex-shrink-0">
                    <x-icons.arrow-left class="w-8 h-8" />
                </a>

                <div class="flex-grow flex items-center justify-between min-w-0">
                    <h1 class="text-3xl sm:text-2xl md:text-3xl text-gray-700 truncate min-w-0 flex-1 mr-4">{{ $deck->name }}</h1>

                    @can('view', $deck)
                        <div class="flex-shrink-0">
                            <x-kebab-menu>
                                @can('edit', $deck)
                                    <button @click="$dispatch('open-edit-modal'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <x-icons.pencil class="w-3 h-3 mr-2" />
                                        <span>Edit Deck</span>
                                    </button>

                                    <button @click="$dispatch('openDeleteDeckModal', { deckId: {{ $deck->id }} }); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <x-icons.trash class="w-3 h-3 mr-2" />
                                        <span>Delete Deck</span>
                                    </button>
                                @else
                                    <button @click="$dispatch('openRemoveDeckSharingModal', { deckId: {{ $deck->id }} }); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <x-icons.trash class="w-3 h-3 mr-2" />
                                        <span>Remove from Shared</span>
                                    </button>
                                @endcan
                            </x-kebab-menu>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Action Buttons Area -->
            <div class="@can('edit', $deck) grid grid-cols-2 gap-4 sm:flex sm:space-x-4 @else flex @endcan justify-center">
                @if($deck->flashcards->count() > 0)
                    <x-button href="{{ route('decks.study', $deck) }}" class="w-full justify-center @can('edit', $deck) @endcan !p-4" size="md" fontWeight="font-bold">
                        <div class="flex flex-col items-center space-y-2">
                            <x-icons.play class="w-6 h-6"/>
                            <span>Study Deck</span>
                        </div>
                    </x-button>
                @else
                    <div class="w-full @can('edit', $deck) @endcan !p-4 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed flex items-center justify-center" title="No cards to study">
                        <div class="flex flex-col items-center space-y-2">
                            <x-icons.play class="w-6 h-6"/>
                            <span class="font-bold">Study Deck</span>
                        </div>
                    </div>
                @endif
                @can('edit', $deck)
                    <x-button variant="secondary" href="{{ route('flashcards.edit', ['deck' => $deck]) }}" class="w-full justify-center !p-4" size="md" fontWeight="font-semibold">
                        <div class="flex flex-col items-center space-y-2">
                            <x-icons.plus-circle class="w-6 h-6"/>
                            <span>New Card</span>
                        </div>
                    </x-button>
                @endcan
            </div>

            <!-- Cards Area -->
            <div>
                <div class="flex justify-between items-center mb-4 mt-8">
                    <h2 class="text-2xl text-gray-700">Cards</h2>
                </div>
                <div class="space-y-4">
                    @forelse($deck->flashcards as $card)
                        <a href="{{ route('flashcards.edit', ['deck' => $deck, 'flashcardId' => $card->id]) }}" wire:key="flashcard-{{ $card->id }}" class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition-all duration-200 hover:bg-gray-50 cursor-pointer">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 line-clamp-3">{{ $card->question }}</h3>
                                    <p class="text-gray-600 line-clamp-3 mt-1">{{ $card->answer }}</p>
                                </div>
                                @can('edit', $deck)
                                <div class="ml-4">
                                    <button wire:click.prevent="$dispatch('openDeleteFlashcardModal', { flashcardId: {{ $card->id }} })" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-gray-100">
                                        <x-icons.trash class="text-primary-700 hover:text-primary-800" />
                                    </button>
                                </div>
                                @endcan
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-gray-500 py-8 bg-white rounded-xl shadow">
                            <p>No cards yet.</p>
                        </div>
                    @endforelse
                </div>

                @if($hasMorePages)
                    <div wire:loading.flex class="justify-center py-4 text-gray-500">
                        Loading more cards...
                    </div>
                    <div x-data="{
                        observe() {
                            let observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        @this.call('loadMore');
                                    }
                                })
                            }, {
                                root: null
                            })
                            observer.observe(this.$el)
                        }
                    }" x-init="observe"></div>
                @endif
            </div>
        </div>
    </div>

    @if($showEditModal)
    <!-- Edit Deck Modal -->
    <div class="px-4 fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-30" wire:click="closeEditModal">
        <div wire:click.stop class="relative bg-white rounded-lg p-6 shadow-xl w-full max-w-md">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Edit Deck
            </h3>

            <!-- Deck Name -->
            <div class="mb-4">
                <label for="deck-name" class="block text-sm font-medium text-gray-700 mb-2">Deck Name</label>
                <input type="text"
                       id="deck-name"
                       wire:model.defer="name"
                       wire:keydown.enter="updateDeck"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500"
                       placeholder="Enter deck name">
                @error('name') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <!-- Deck Visibility -->
            <div class="mb-6">
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

                @if($isPublic)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start mb-2">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-blue-800 font-medium">Public URL</p>
                                <p class="text-xs text-blue-700 mt-1">Anyone can access this deck using the URL below:</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-white rounded border px-3 py-2">
                                <input
                                    type="text"
                                    id="public-url"
                                    value="{{ route('public.decks.study', $deck) }}"
                                    readonly
                                    class="w-full text-xs text-gray-700 bg-transparent border-none focus:ring-0 p-0"
                                >
                            </div>
                            <button
                                onclick="copyToClipboard('{{ route('public.decks.study', $deck) }}')"
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded border border-blue-600 hover:border-blue-700 transition-colors flex items-center"
                                title="Copy URL"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="sm:flex sm:flex-row-reverse">
                <x-button wire:click="updateDeck" size="md" class="w-full sm:w-auto sm:ml-3">
                    Save Changes
                </x-button>
                <x-button wire:click="closeEditModal" variant="secondary" size="md" class="w-full sm:w-auto mt-3 sm:mt-0">
                    Cancel
                </x-button>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a temporary success message
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 1500);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
    @endif
    <livewire:modals.delete-flashcard-modal />
    <livewire:modals.delete-deck-modal />
    <livewire:modals.remove-deck-sharing-modal />
</div>
