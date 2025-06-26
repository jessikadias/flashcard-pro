@extends('layouts.base')

@section('title', $title ? $title . ' - ' . config('app.name') : config('app.name'))

@section('body-class', 'font-sans text-gray-900 antialiased')

@section('body')
    <div class="min-h-screen bg-primary-50">
        <!-- Top blue section with logo -->
        <div class="bg-primary-800 pb-16 pt-4 px-4">
            <div class="max-w-md mx-auto text-center">
                <a href="/">
                    <img src="{{ asset('images/flashcardpro-logo.png') }}" alt="FlashCard Pro Logo" class="w-20 h-26 mx-auto">
                </a>
                <h1 class="text-2xl font-bold text-white">Welcome to FlashCard Pro</h1>
            </div>
        </div>

        <!-- Card content -->
        <div class="max-w-md mx-auto px-4 -mt-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
@endsection
