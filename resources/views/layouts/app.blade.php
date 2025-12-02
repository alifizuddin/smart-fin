<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartFin360') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
        
        @livewireScripts
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 h-full" x-data="{ 
    sidebarOpen: window.innerWidth >= 1024,
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleTheme() {
        this.darkMode = !this.darkMode;
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}" x-init="window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024 }); if (darkMode) { document.documentElement.classList.add('dark') }">
        @auth
            @livewire('layout.sidebar')
        
        <!-- Page Content -->
        <main 
            class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'"
        >
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{ $slot }}
        </main>
        @else
            <div class="min-h-screen flex flex-col">
                @if (Route::has('login'))
                    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between h-16">
                                <div class="flex">
                                    <div class="shrink-0 flex items-center">
                                        <a href="{{ url('/') }}">
                                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                        </a>
                                    </div>
                                </div>

                                <div class="hidden sm:flex sm:items-center sm:ms-6">
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</a>
                                    @else
                                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">Log in</a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">Register</a>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </nav>
                @endif

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        @endif

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @stack('scripts')
        @yield('content')
        @yield('scripts')
        <script src="{{ asset('js/confirmation-modal.js') }}"></script>
        <x-chatbot-widget />
    </body>
</html>