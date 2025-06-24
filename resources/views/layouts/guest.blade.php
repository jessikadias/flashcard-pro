<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
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
    </body>
</html>
