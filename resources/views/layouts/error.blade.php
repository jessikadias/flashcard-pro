@extends('layouts.base')

@section('title', '@yield("title") - ' . config('app.name'))

@section('body')
    <div class="min-h-screen bg-primary-50 flex flex-col">
        <!-- Header with Logo -->
        <div class="bg-primary-800 py-8 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <a href="/" class="inline-block">
                    <img src="{{ asset('images/flashcardpro-logo.png') }}" alt="FlashCard Pro Logo" class="w-16 h-20 mx-auto mb-4">
                </a>
                <h1 class="text-2xl font-bold text-white">FlashCard Pro</h1>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="max-w-2xl mx-auto text-center">
                <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-12">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Footer -->
        <x-footer />
    </div>
@endsection