<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Smart Healthcare') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-900">
        <div class="min-h-screen flex flex-col justify-between items-center py-6 px-4 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900">
            <div class="w-full flex justify-between items-center max-w-4xl px-4">
                <a href="/" class="flex items-center space-x-2 text-white font-bold text-xl">
                    <i class="bi bi-heart-pulse-fill text-red-500 text-2xl"></i>
                    <span>Smart Healthcare</span>
                </a>
                <a href="/" class="text-sm text-teal-300 hover:text-white transition">
                    <i class="bi bi-arrow-left me-1"></i> Back to Home
                </a>
            </div>

            <div class="w-full sm:max-w-md my-auto px-6 py-6 bg-white/95 backdrop-blur-md shadow-2xl overflow-hidden rounded-2xl border border-white/20">
                {{ $slot }}
            </div>

        </div>
    </body>
</html>
