<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/main.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="p-guest-layout">
            <div class="p-guest-layout__logo">
                <a href="/">
                    {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                    <span class="p-guest-layout__logo-text">Rese</span>
                </a>
            </div>

            <div class="p-guest-layout__card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
