@extends('layouts.base')

@section('title', $title ? $title . ' - ' . config('app.name') : config('app.name'))

@push('styles')
    @livewireStyles
@endpush

@section('body')
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Flash Messages -->
        <x-notifications />

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @auth
        <livewire:onboarding-tutorial />
    @endauth
@endsection

@push('scripts')
    @livewireScripts
@endpush