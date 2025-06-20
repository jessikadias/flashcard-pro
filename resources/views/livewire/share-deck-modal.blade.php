<div>
    @if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center px-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="close"></div>
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#223464]/10 sm:mx-0 sm:h-10 sm:w-10">
                        <x-icons.share class="text-[#223464]" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Share Deck
                        </h3>
                        @if($deck)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Enter the email address of the user you want to share "{{ $deck->name }}" with.
                            </p>
                            <div>
                                <input
                                    wire:model="shareEmail"
                                    type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#223464] focus:ring focus:ring-[#223464] focus:ring-opacity-50"
                                    placeholder="user@example.com"
                                    wire:keydown.enter="shareDeck"
                                >
                                @error('shareEmail')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                <x-button wire:click="shareDeck" size="md" class="w-full sm:w-auto sm:ml-3">
                    Share
                </x-button>
                <x-button wire:click="close" variant="secondary" size="md" class="w-full sm:w-auto mt-3 sm:mt-0">
                    Cancel
                </x-button>
            </div>
        </div>
    </div>
    @endif
</div>
