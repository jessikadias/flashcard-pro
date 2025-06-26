@extends('layouts.error')

@section('title', 'Access Forbidden')

@section('content')
    <!-- Error Image -->
    <div class="mb-8">
        <img src="{{ asset('images/403.png') }}" alt="403 Error" class="w-64 h-64 mx-auto object-contain">
    </div>

    <!-- Error Code -->
    <div class="text-6xl font-bold text-red-600 mb-4">403</div>

    <!-- Error Title -->
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Access Forbidden</h1>

    <!-- Error Description -->
    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
        You don't have permission to access this resource.
        It looks like this flashcard deck is off-limits to you right now.
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
                Login to Continue
            </a>
        @endauth
    </div>

    <!-- Additional Info -->
    <div class="mt-8 pt-8 border-t border-gray-200">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <x-icons.exclamation-triangle class="h-5 w-5 text-yellow-400 mr-3 mt-0.5" />
                <div class="text-sm text-yellow-800">
                    <strong>Need access?</strong> If you believe you should have access to this resource,
                    please contact the owner or check your account permissions.
                </div>
            </div>
        </div>
    </div>
@endsection