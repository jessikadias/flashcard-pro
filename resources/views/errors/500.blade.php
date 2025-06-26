@extends('layouts.error')

@section('title', 'Server Error')

@section('content')
    <!-- Error Image -->
    <div class="mb-8">
        <img src="{{ asset('images/500.png') }}" alt="500 Error" class="w-64 h-64 mx-auto object-contain">
    </div>

    <!-- Error Code -->
    <div class="text-6xl font-bold text-red-600 mb-4">500</div>

    <!-- Error Title -->
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Internal Server Error</h1>

    <!-- Error Description -->
    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
        Oops! Something went wrong on our end. Our servers are having trouble processing your request.
        Don't worry, our team has been notified and we're working to fix this issue.
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="window.location.reload()"
                class="inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-md font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out">
            <x-icons.arrow-path class="w-5 h-5 mr-2" />
            Try Again
        </button>

        <a href="{{ url('/') }}"
           class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150 ease-in-out">
            <x-icons.arrow-left class="w-5 h-5 mr-2" />
            Back to Home
        </a>
    </div>

    <!-- Status Update -->
    <div class="mt-8 pt-8 border-t border-gray-200">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <x-icons.information-circle class="h-5 w-5 text-blue-400 mr-3 mt-0.5" />
                <div class="text-sm text-blue-800">
                    <strong>What's happening?</strong> We're experiencing technical difficulties.
                    Please try refreshing the page in a few moments. If the problem persists,
                    it should be resolved shortly.
                </div>
            </div>
        </div>
    </div>

    <!-- Helpful Actions -->
    <div class="mt-6">
        <p class="text-sm text-gray-500 mb-4">While you wait, you can:</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            @auth
                <a href="{{ route('decks.index') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                    • Check your flashcard decks
                </a>
                <a href="{{ route('profile.edit') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                    • Update your profile
                </a>
            @else
                <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                    • Sign in to your account
                </a>
                <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 transition duration-150 ease-in-out">
                    • Create a new account
                </a>
            @endauth
        </div>
    </div>
@endsection