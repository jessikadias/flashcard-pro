<section id="api-token">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('API Token') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Generate an API token to access your account programmatically. Keep your token secure and do not share it with others.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @if (session('status') === 'api-token-generated')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">{{ __('API Token Generated!') }}</strong>
                <span class="block sm:inline">{{ __('Your new API token is ready to use.') }}</span>
            </div>
        @endif

        @if (session('status') === 'api-token-revoked')
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">{{ __('API Token Revoked!') }}</strong>
                <span class="block sm:inline">{{ __('Your API token has been successfully revoked.') }}</span>
            </div>
        @endif

        @if (auth()->user()->api_token)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800">{{ __('Active API Token') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>{{ __('You have an active API token. You can revoke it at any time if it becomes compromised.') }}</p>
                        </div>
                        
                        <div class="mt-3">
                            <label for="current-api-token" class="block text-sm font-medium text-gray-700">{{ __('Your API Token') }}</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" 
                                       id="current-api-token" 
                                       value="{{ auth()->user()->api_token }}" 
                                       readonly 
                                       class="flex-1 block w-full rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50 font-mono text-xs">
                                <button type="button" 
                                        onclick="navigator.clipboard.writeText(document.getElementById('current-api-token').value).then(() => { this.textContent='Copied!'; this.classList.add('bg-green-100','text-green-700'); setTimeout(() => { this.textContent='Copy'; this.classList.remove('bg-green-100','text-green-700'); }, 2000); })"
                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    {{ __('Copy') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <form method="post" action="{{ route('profile.api-token.revoke') }}" class="inline">
                                @csrf
                                @method('delete')
                                
                                <x-danger-button 
                                    onclick="return confirm('{{ __('Are you sure you want to revoke your API token? This action cannot be undone and will break any applications using this token.') }}')">
                                    {{ __('Revoke Token') }}
                                </x-danger-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No API Token') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Generate an API token to access your account programmatically.') }}</p>
                    <div class="mt-4">
                        <form method="post" action="{{ route('profile.api-token.generate') }}">
                            @csrf
                            
                            <x-primary-button>
                                {{ __('Generate API Token') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('API Usage') }}</h4>
            <div class="text-sm text-gray-600 space-y-2">
                <p>{{ __('Include your API token in the Authorization header:') }}</p>
                <code class="block bg-gray-100 p-2 rounded text-xs">Authorization: Bearer YOUR_API_TOKEN</code>
                <p>{{ __('API Base URL:') }} <code class="bg-gray-100 px-1 rounded">{{ url('/api') }}</code></p>
                <p>{{ __('Documentation:') }} <a href="/api/status" class="text-blue-600 hover:text-blue-800 underline">{{ __('API Status Endpoint') }}</a></p>
            </div>
        </div>
    </div>
</section>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show feedback
        const button = element.nextElementSibling;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-100', 'text-green-700');
        
        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('bg-green-100', 'text-green-700');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}
</script> 