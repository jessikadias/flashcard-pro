@extends('layouts.error')

@section('title', 'Page Not Found')

@section('content')
    <!-- Error Image -->
    <div class="mb-8">
        <img src="{{ asset('images/404.png') }}" alt="404 Error" class="w-64 h-64 mx-auto object-contain">
    </div>

    <!-- Error Code -->
    <div class="text-6xl font-bold text-primary-800 mb-4">404</div>

    <!-- Error Title -->
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Oops! Page Not Found</h1>

    <!-- Error Description -->
    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
        The page you're looking for seems to have vanished into thin air.
        Don't worry, even the best flashcards sometimes get misplaced!
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url('/') }}"
           class="inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-md font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out">
            <x-icons.arrow-left class="w-5 h-5 mr-2" />
            Back to Home
        </a>

        @auth
            <a href="{{ route('decks.index') }}"
               class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                <x-icons.queue-list class="w-5 h-5 mr-2" />
                View My Decks
            </a>
        @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Login
            </a>
        @endauth
    </div>

    <!-- Helpful Links -->
    <div class="mt-8 pt-8 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-4">Looking for something specific?</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <a href="{{ url('/') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                • Home Page
            </a>
            @auth
                <a href="{{ route('decks.index') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                    • My Flashcard Decks
                </a>
            @else
                <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                    • Create Account
                </a>
            @endauth
        </div>
    </div>
@endsection