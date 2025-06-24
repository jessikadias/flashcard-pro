<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-sm">

        @if($sessionStarted && !$sessionCompleted)
            <!-- Study Session -->
            <div>
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between items-center text-sm font-medium text-gray-500">
                        <span>Card</span>
                        <span>{{ $currentCardIndex + 1 }} of {{ $totalCards }}</span>
                    </div>
                    <div class="w-full bg-white rounded-full h-1.5 mt-2">
                        <div class="bg-primary-500 h-1.5 rounded-full" style="width: {{ $this->getProgressPercentage() }}%"></div>
                    </div>
                </div>

                <!-- Flippable Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    @if($this->currentCard)
                        <div class="p-6 flex flex-col justify-center text-center" style="min-height: 350px;">
                            @if($showQuestion)
                                <!-- Question Side -->
                                <span class="text-sm text-gray-400 font-semibold">Question</span>
                                <hr class="w-24 my-3 mx-auto border-gray-200">
                                <p class="text-2xl text-gray-800 font-semibold leading-snug">
                                    {{ $this->currentCard->question }}
                                </p>
                            @else
                                <!-- Answer Side -->
                                <p class="text-4xl text-gray-800 font-bold leading-tight">
                                    {{ $this->currentCard->answer }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6">
                    @if($showQuestion)
                        <x-button wire:click="flipCard" variant="primary" class="w-full !py-4 !rounded-xl !font-bold">
                            FLIP CARD
                        </x-button>
                    @else
                        <div class="flex justify-center space-x-3">
                            <x-button wire:click="markIncorrect" variant="secondary" class="w-full !h-20 !rounded-xl">
                                <x-icons.thumbs-down class="w-8 h-8 text-primary-500" />
                            </x-button>
                            <x-button wire:click="markCorrect" variant="primary" class="w-full !h-20 !rounded-xl">
                                <x-icons.thumbs-up class="w-8 h-8 text-white" />
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($sessionCompleted)
            <!-- Session Results -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 text-center">
                    <div class="text-6xl mb-4">{{ $this->results['emoji'] }}</div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $this->results['title'] }}</h2>
                    <p class="text-gray-600 mb-8 max-w-xs mx-auto">{{ $this->results['subtitle'] }}</p>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <x-button wire:click="restartSession" variant="primary" class="w-full !rounded-xl">
                            <x-icons.arrow-path class="w-5 h-5 mr-2" />
                            Let's do it again!
                        </x-button>
                        
                        <x-button href="{{ route('decks.edit', $deck) }}" variant="secondary" class="w-full !rounded-xl">
                            <x-icons.arrow-left class="w-5 h-5 mr-2" />
                            I'm done for now
                        </x-button>
                    </div>
                </div>
            </div>

        @else
            <!-- Session Setup -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Hey, {{ $this->user->name }}!</h2>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">How would you like to study this deck?</h2>
                        
                        <!-- Study Options -->
                        <div class="mt-8 mb-6 flex justify-center space-x-4">
                            <x-button wire:click="startSession('sequential')" variant="primary" class="flex-1 !p-4">
                                <div class="flex flex-col items-center space-y-2">
                                    <x-icons.queue-list class="w-8 h-8"/>
                                    <span>Sequential</span>
                                </div>
                            </x-button>
                            <x-button wire:click="startSession('random')" variant="secondary" class="flex-1 !p-4">
                                <div class="flex flex-col items-center space-y-2">
                                    <x-icons.arrows-right-left class="w-8 h-8"/>
                                    <span>Random</span>
                                </div>
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Exit Link -->
        <div class="text-center mt-4">
            <a href="{{ route('decks.edit', $deck) }}" class="text-sm text-gray-600 hover:text-gray-800 transition">
                Exit Study Session
            </a>
        </div>
    </div>
</div>
