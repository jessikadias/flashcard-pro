@extends('layouts.base')

@section('title', config('app.name'))

@section('body')
    <div class="min-h-screen bg-primary-50">
        <!-- Top blue section with logo and hero -->
        <div class="bg-primary-800 pb-16 pt-8 px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Navigation -->
                @if (Route::has('login'))
                    <nav class="flex items-center justify-between mb-12">
                        <div class="flex items-center">
                            <img src="{{ asset('images/flashcardpro-logo.png') }}" alt="FlashCard Pro Logo" class="w-12 h-18 mr-3">
                            <span class="text-xl font-bold text-white">FlashCard Pro</span>
                        </div>
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ route('decks.index') }}" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                                    My Decks
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-6 py-2 bg-white text-primary-800 hover:bg-primary-50 rounded-lg font-medium transition-colors">
                                        Get Started
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </nav>
                @endif

                <!-- Hero Section -->
                <div class="text-center text-white">
                    <h1 class="text-3xl font-bold mb-6">Master Any Subject with FlashCard Pro</h1>
                    <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                        Create, study, and share flashcards with our intuitive platform.
                        Perfect for students, professionals, and lifelong learners.
                    </p>
                    @guest
                        <div class="flex justify-center gap-4">
                            <a href="{{ route('login') }}" class="px-8 py-4 border-2 border-white text-white hover:bg-white hover:text-primary-800 rounded-lg font-bold text-lg transition-colors">
                                Sign In
                            </a>
                        </div>
                    @else
                        <a href="{{ route('decks.index') }}" class="px-8 py-4 bg-white text-primary-800 hover:bg-primary-50 rounded-lg font-bold text-lg transition-colors">
                            Go to My Decks
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-4xl mx-auto px-4 py-16 -mt-8">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.168 18.477 18.582 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Create & Organize</h3>
                    <p class="text-gray-600">
                        Easily create flashcard decks and organize them by subject, difficulty, or any system that works for you.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Smart Study</h3>
                    <p class="text-gray-600">
                        Our intuitive study interface helps you focus on what matters most and track your progress over time.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Share & Collaborate</h3>
                    <p class="text-gray-600">
                        Share your flashcard decks with classmates, colleagues, or study groups for collaborative learning.
                    </p>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        @guest
            <div class="bg-white border-t border-gray-200">
                <div class="max-w-4xl mx-auto px-4 py-16 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Ready to Start Learning?</h2>
                    <p class="text-xl text-gray-600 mb-8">Join thousands of students and professionals who trust FlashCard Pro.</p>
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-primary-800 hover:bg-primary-700 text-white rounded-lg font-bold text-lg transition-colors">
                        Create Your Free Account
                    </a>
                </div>
            </div>
        @endguest

        <!-- Footer -->
        <x-footer />
    </div>
@endsection
