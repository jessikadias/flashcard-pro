<div class="min-h-screen bg-[#f3f6fa]">
    <!-- Top blue section -->
    <div class="bg-[#223464] pb-12 pt-9 px-4">
        <div class="max-w-xl mx-auto">
            <h1 class="text-3xl font-bold text-white text-center mb-6">My Decks</h1>
            <div class="flex justify-center">
                <div class="flex items-center bg-[#2d437c] rounded-lg px-4 py-0.5 w-48 mr-2.5 shadow">
                    <svg class="w-5 h-5 text-gray-300 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Search decks"
                        class="w-full bg-transparent outline-none text-white placeholder-gray-300 text-md border-0 focus:ring-0"
                    />
                </div>
                <a href="{{ route('decks.create') }}"
                   class="bg-[#315A92] hover:bg-blue-600 text-white rounded-lg px-4 py-0.5 shadow text-md transition flex items-center">
                    + New Deck
                </a>
            </div>
        </div>
    </div>

    <!-- Deck List -->
    <div class="max-w-xl mx-auto space-y-2 px-4 py-2 -mt-8">
        @forelse($decks as $deck)
            <div wire:key="deck-{{ $deck->id }}" class="flex items-center bg-white rounded-xl shadow-lg p-5">
                <div class="bg-[#f3f6fa] rounded-lg p-3 mr-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#244164]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="5" y="7" width="14" height="10" rx="2" />
                        <rect x="7.5" y="9.5" width="14" height="10" rx="2" stroke-opacity="0.5"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-xl font-bold text-gray-900">{{ $deck->name }}</div>
                    <div class="text-gray-500 text-base">{{ $deck->flashcards_count }} cards</div>
                </div>
                <div class="flex items-center gap-4 ml-4">
                    <!-- Ícone de compartilhar -->
                    <button wire:click="shareDeck({{ $deck->id }})" type="button">
                        <svg class="w-5 h-5 text-[#244164] hover:text-blue-700 cursor-pointer" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="18" cy="5" r="3"/>
                            <circle cx="6" cy="12" r="3"/>
                            <circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                    </button>
                    <!-- Ícone de remover -->
                    <button wire:click="deleteDeck({{ $deck->id }})" type="button">
                        <svg class="w-5 h-7 text-[#244164] hover:text-red-600 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            <line x1="3" y1="7" x2="21" y2="7" stroke-linecap="round" />
                            <rect x="5" y="7" width="14" height="16" rx="2" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-12 text-lg">No decks found.</div>
        @endforelse
    </div>

    <div wire:loading.flex class="justify-center py-4 text-gray-500">
        Loading more decks...
    </div>

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