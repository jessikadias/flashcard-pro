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
                <a href="{{ route('decks.edit', ['deck' => $deck]) }}" class="flex-1 min-w-0 flex items-center cursor-pointer">
                    <div class="bg-[#f3f6fa] rounded-lg p-3 mr-4 flex items-center justify-center">
                        <x-icons.deck class="text-[#244164]" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xl font-bold text-gray-700 truncate">{{ $deck->name }}</div>
                        <div class="text-gray-500 text-base">{{ $deck->flashcards_count }} cards</div>
                    </div>
                </a>
               
                <div class="flex items-center ml-4">
                    <a href="{{ route('decks.study', $deck) }}" class="rounded-lg transition-all duration-200 hover:bg-[#f3f6fa]">
                        <x-icons.play class="text-[#244164] hover:text-[#223464] cursor-pointer" />
                    </a>
                    @if($this->canEdit($deck))
                        <button wire:click.prevent="$dispatch('openShareDeckModal', { deckId: {{ $deck->id }} })" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-[#f3f6fa]">
                            <x-icons.share class="text-[#244164] hover:text-[#223464] cursor-pointer" />
                        </button>
                        <button wire:click.prevent="$dispatch('openDeleteDeckModal', { deckId: {{ $deck->id }} })" type="button" class="p-2 rounded-lg transition-all duration-200 hover:bg-[#f3f6fa]">
                            <x-icons.trash class="text-[#244164] hover:text-[#223464] cursor-pointer" />
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