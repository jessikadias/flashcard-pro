<div class="min-h-screen bg-primary-50">
    <!-- Top blue section -->
    <div class="bg-primary-800 pb-12 pt-9 px-4">
        <div class="max-w-xl mx-auto">
            <h1 class="text-3xl font-bold text-white text-center mb-6">My Decks</h1>
            <div class="flex justify-center">
                <div class="flex items-center bg-primary-600 rounded-lg px-4 py-0.5 w-48 mr-2.5 shadow">
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
                @if($deck->flashcards_count > 0)
                    <a href="{{ route('decks.study', $deck) }}" class="flex-shrink-0 mr-4">
                        <div class="bg-primary-50 rounded-lg p-3 flex items-center justify-center hover:bg-primary-100 transition-colors">
                            <x-icons.play class="text-primary-700" />
                        </div>
                    </a>
                @else
                    <div class="flex-shrink-0 mr-4">
                        <div class="bg-gray-100 rounded-lg p-3 flex items-center justify-center">
                            <x-icons.play class="text-gray-400" />
                        </div>
                    </div>
                @endif
                
                <a href="{{ route('decks.edit', $deck) }}" class="flex-1 min-w-0 flex items-center cursor-pointer">
                    <div class="flex-1 min-w-0">
                        <div class="text-xl font-bold text-gray-700 truncate">{{ $deck->name }}</div>
                        <div class="text-gray-500 text-base">
                            {{ $deck->flashcards_count }} cards
                            @if(!$this->isOwner($deck))
                                • Shared by {{ $deck->user->name }}
                            @endif
                        </div>
                    </div>
                </a>
               
                <div class="flex items-center ml-4 space-x-2">
                    @if($this->isOwner($deck))
                        <button wire:click.prevent="$dispatch('openShareDeckModal', { deckId: {{ $deck->id }} })" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-primary-50">
                            <x-icons.share class="text-primary-700 hover:text-primary-800 cursor-pointer" />
                        </button>
                        <button wire:click.prevent="$dispatch('openDeleteDeckModal', { deckId: {{ $deck->id }} })" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-primary-50">
                            <x-icons.trash class="text-primary-700 hover:text-primary-800 cursor-pointer" />
                        </button>
                    @else
                        <button wire:click.prevent="$dispatch('openRemoveDeckSharingModal', { deckId: {{ $deck->id }} })" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-primary-50">
                            <x-icons.trash class="text-primary-700 hover:text-primary-800 cursor-pointer" />
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-12 text-lg">No decks found.</div>
        @endforelse
    </div>

    <div wire:loading.flex class="justify-center py-4 text-gray-500">
        Loading more decks...
    </div>

    <livewire:create-deck-modal />
    <livewire:share-deck-modal />
    <livewire:delete-deck-modal />
    <livewire:remove-deck-sharing-modal />

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