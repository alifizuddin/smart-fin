<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartFin360</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900">

            <main class="flex items-center justify-center w-full p-4">
                <div class="w-full max-w-5xl mx-auto flex flex-col md:flex-row bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
                    <div class="hidden md:block md:w-1/2">
                        <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1664575602276-acd073f104c1?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8YnVzaW5lc3N8ZW58MHx8MHx8fDA%3D&fm=jpg&q=60&w=3000" alt="Financial Planning Meeting">
                    </div>

                    <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-8 sm:p-12">
                        <div class="w-full">
                            <div class="flex justify-center mb-6">
                                @if (request()->routeIs('login'))
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white text-center">
                                        Login
                                    </h1>
                                @elseif (request()->routeIs('register'))
                                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center">
                                        Sign Up
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center mt-2">Please sign up to continue.</p>
                                @else
                                    {{-- Fallback for other guest pages like forgot-password --}}
                                    <a href="/" wire:navigate>
                                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                                    </a>
                                @endif
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>