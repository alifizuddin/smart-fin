<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartFin360') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // Enable dark mode if system preference is dark
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        
        <style>
            .abstract-bg {
                background-image: url('https://images.unsplash.com/photo-1696550579911-4ece0b1899ea?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxjb2xsZWN0aW9uLXBhZ2V8MTF8MTE1Mzc1fHxlbnwwfHx8fHw%3D');
                background-size: cover;
                background-position: center;
            }
            .glass-effect {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            .dark .glass-effect {
                background: rgba(15, 23, 42, 0.7);
            }
        </style>
    </head>
    <body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
        <nav class="fixed top-4 left-1/2 -translate-x-1/2 w-[95%] max-w-full z-50">
            <div class="glass-effect border border-white/20 dark:border-slate-700/30 rounded-full px-6 py-3 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white font-bold italic">S</div>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 tracking-tight">SmartFin360</span>
                </div>
                <div class="flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-400">
                    <a class="hover:text-primary transition-colors" href="#">Support</a>
                    <a class="hover:text-primary transition-colors" href="#">Documentation</a>
                    <button class="p-2 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 rounded-full transition-all" onclick="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'">
                        <span class="material-symbols-outlined text-[20px] block dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined text-[20px] hidden dark:block text-yellow-400">light_mode</span>
                    </button>
                </div>
            </div>
        </nav>

        <main class="flex-grow flex flex-col lg:flex-row h-screen">
            <div class="w-full lg:w-[40%] flex items-center justify-center p-8 bg-white dark:bg-slate-900 relative">
                <div class="w-full max-w-md space-y-8">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex items-center gap-3 mb-6">
                            <svg class="text-primary" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 5L25 20L10 35H18L33 20L18 5H10Z" fill="currentColor"></path>
                                <path d="M5 5L20 20L5 35H13L28 20L13 5H5Z" fill="currentColor" fill-opacity="0.5"></path>
                            </svg>
                            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">SmartFin360</h1>
                        </div>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">
                            @if (request()->routeIs('register'))
                                Create your account
                            @elseif (request()->routeIs('password.request'))
                                Reset your password
                            @else
                                Welcome back
                            @endif
                        </h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-2">
                            @if (request()->routeIs('register'))
                                Create your account to get started
                            @elseif (request()->routeIs('password.request'))
                                Enter your email to reset your password
                            @else
                                Sign in to your account to continue
                            @endif
                        </p>
                    </div>

                    {{ $slot }}
                </div>
            </div>
            
            <div class="hidden lg:block lg:w-[60%] abstract-bg relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-tr from-primary/40 to-transparent mix-blend-multiply"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center p-12 text-white z-10">
                    <div class="glass-effect p-12 rounded-3xl max-w-lg border border-white/20 shadow-2xl">
                        <div class="mb-8">
                            <span class="inline-block px-3 py-1 bg-white/20 border border-white/30 rounded-full text-xs font-bold tracking-widest uppercase mb-4">Enterprise Edition</span>
                            <h2 class="text-4xl font-bold leading-tight mb-4">Smart Financial Management</h2>
                            <p class="text-lg text-white/80 leading-relaxed">
                                SmartFin360 provides intelligent insights and financial management tools to help you take control of your business finances.
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex -space-x-3">
                                <img alt="Team member" class="w-10 h-10 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/32.jpg">
                                <img alt="Team member" class="w-10 h-10 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/44.jpg">
                                <img alt="Team member" class="w-10 h-10 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/75.jpg">
                            </div>
                            <p class="text-sm font-medium text-white/90">Join 1,000+ businesses worldwide</p>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-12 right-12 flex gap-4 text-white/50 text-sm font-medium">
                    <span>Privacy Policy</span>
                    <span>Terms of Service</span>
                    <span>© {{ date('Y') }} SmartFin360 Inc.</span>
                </div>
            </div>
        </main>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>