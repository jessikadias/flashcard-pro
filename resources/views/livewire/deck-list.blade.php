<div class="min-h-screen bg-[#f3f6fa]">
    <!-- Top blue section -->
    <div class="bg-[#223464] pb-12 pt-9 px-4">
        <div class="max-w-xl mx-auto">
            <h1 class="text-3xl font-bold text-white text-center mb-6">My Decks</h1>
            <div class="flex justify-center">
                <div class="flex items-center bg-[#2d437c] rounded-lg px-4 py-0.5 w-48 mr-2.5 shadow">
                    <x-icons.search class="text-gray-300 mr-2" />
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Search decks"
                        class="w-full bg-transparent outline-none text-white placeholder-gray-300 text-md border-0 focus:ring-0"
                    />
                </div>
                <x-button wire:click="$dispatch('openCreateDeckModal')" variant="header" size="sm" fontWeight="font-normal">
                    <x-icons.plus class="w-6 h-6 mr-2" stroke-width="1.5" />
                    New Deck
                </x-button>
            </div>
        </div>
    </div>

    <!-- Deck List -->
    <div class="max-w-xl mx-auto space-y-2 px-4 py-2 -mt-8">
        @forelse($decks as $deck)
            <div wire:key="deck-{{ $deck->id }}" class="flex items-center bg-white rounded-xl shadow-lg p-5 transition-all duration-200 hover:shadow-xl hover:bg-gray-50">
                <div class="bg-[#f3f6fa] rounded-lg p-3 mr-4 flex items-center justify-center">
                    <x-icons.deck class="text-[#244164]" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xl font-bold text-gray-700 truncate">{{ $deck->name }}</div>
                    <div class="text-gray-500 text-base">{{ $deck->flashcards_count }} cards</div>
                </div>
                @if($this->canEdit($deck))
                    <div class="flex items-center gap-4 ml-4">
                        <!-- Ícone de compartilhar -->
                        <button wire:click="openShareModal({{ $deck->id }})" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-[#f3f6fa]">
                            <x-icons.share class="text-[#244164] hover:text-[#223464] cursor-pointer" />
                        </button>
                        <!-- Ícone de remover -->
                        <button wire:click="confirmDeletion({{ $deck->id }})" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-[#f3f6fa]">
                            <x-icons.trash class="text-[#244164] hover:text-[#223464] cursor-pointer" />
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-gray-400 py-12 text-lg">No decks found.</div>
        @endforelse
    </div>

    <div wire:loading.flex class="justify-center py-4 text-gray-500">
        Loading more decks...
    </div>

    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#223464]/10 sm:mx-0 sm:h-10 sm:w-10">
                                <!-- Delete icon -->
                                <svg class="h-6 w-6 text-[#223464]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete Deck
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete "{{ $selectedDeck->name }}"? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="deleteDeck"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#223464] text-base font-medium text-white hover:bg-[#315A92] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223464] sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button wire:click="cancelDelete"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showShareModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <!-- Modal panel -->
                <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#223464]/10 sm:mx-0 sm:h-10 sm:w-10">
                                <!-- Share icon -->
                                <svg class="h-6 w-6 text-[#223464]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="18" cy="5" r="3"/>
                                    <circle cx="6" cy="12" r="3"/>
                                    <circle cx="18" cy="19" r="3"/>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Share Deck
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Enter the email address of the user you want to share this deck with.
                                    </p>
                                    <div>
                                        <input
                                            wire:model="shareEmail"
                                            type="email"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#223464] focus:ring focus:ring-[#223464] focus:ring-opacity-50"
                                            placeholder="user@example.com"
                                        >
                                        @error('shareEmail')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="shareDeck"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#223464] text-base font-medium text-white hover:bg-[#315A92] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223464] sm:ml-3 sm:w-auto sm:text-sm">
                            Share
                        </button>
                        <button wire:click="cancelShare"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let loading = false;

            const handleScroll = () => {
                if (loading) return;

                // Trigger when close to bottom
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                    loading = true;
                    Livewire.dispatch('loadMoreDecks');
                    setTimeout(() => loading = false, 1000);
                }
            };

            // Reset scroll and remove all decks from view (simulate full refresh)
            Livewire.on('searchUpdated', () => {
                loading = false;
                window.scrollTo(0, 0);
            });

            // Listen for the loadMoreDecks event and call Livewire method
            Livewire.on('loadMoreDecks', () => {
                Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('loadMore');
            });

            window.addEventListener('scroll', handleScroll);
        });
    </script>
</div>