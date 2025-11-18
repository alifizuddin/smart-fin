<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartFin360') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen w-full flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8" style="background-color: #4494f0;">
            
            <div class="w-full max-w-5xl">
                <div class="bg-white rounded-[1rem] shadow-2xl overflow-hidden flex flex-col md:flex-row">
                    
                    <div class="hidden md:block md:w-5/12 lg:w-5/12 bg-gray-200">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/img3.webp"
                             alt="login form" 
                             class="w-full h-full object-cover rounded-l-[1rem]" />
                    </div>

                    <div class="w-full md:w-7/12 lg:w-7/12 flex items-center">
                        <div class="w-full p-8 md:p-12 lg:p-16 text-black">

                            <div class="flex items-center mb-6 pb-1">
                                <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #ff6219;">
                                    <path d="M21 7.5L12 2.5L3 7.5M21 7.5V16.5L12 21.5M21 7.5L12 12.5M3 7.5V16.5L12 21.5M3 7.5L12 12.5M12 12.5V21.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="text-2xl font-bold tracking-tight">SmartFin360</span>
                            </div>

                            <h5 class="text-xl font-normal mb-6 tracking-wide text-gray-700" style="letter-spacing: 1px;">
                                @if (request()->routeIs('register'))
                                    Create your account
                                @elseif (request()->routeIs('password.request'))
                                    Reset your password
                                @else
                                    Sign into your account
                                @endif
                            </h5>

                            {{ $slot }}

                            <div class="mt-8 pt-2 flex gap-4 text-sm text-gray-500">
                                <a href="#!" class="hover:text-gray-800 transition-colors">Terms of use.</a>
                                <a href="#!" class="hover:text-gray-800 transition-colors">Privacy policy</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>