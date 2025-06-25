@if(session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="fixed bottom-4 right-4 z-50 space-y-2">
        @if(session()->has('success'))
            <x-notification type="success" :message="session('success')" />
        @endif

        @if(session()->has('error'))
            <x-notification type="error" :message="session('error')" />
        @endif

        @if(session()->has('warning'))
            <x-notification type="warning" :message="session('warning')" />
        @endif

        @if(session()->has('info'))
            <x-notification type="info" :message="session('info')" />
        @endif
    </div>
@endif 