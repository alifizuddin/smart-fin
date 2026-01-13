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
        <div class="min-h-screen w-full flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8" style="background-color: #393f81;">
            
            <div class="w-full max-w-7xl">
                <div class="bg-white rounded-[1rem] shadow-2xl overflow-hidden flex flex-col md:flex-row">
                    
                    <div class="hidden md:block md:w-5/12 lg:w-5/12 bg-gray-200">
                        <img src="https://images.stockcake.com/public/5/d/1/5d1e0df2-31a3-4206-a733-0f5e609a97a9/vibrant-abstract-art-stockcake.jpg"
                            alt="login form" 
                            class="w-full h-full object-cover rounded-l-[1rem]" />
                    </div>

                    <div class="w-full md:w-7/12 lg:w-7/12 flex items-center">
                        <div class="w-full p-8 md:p-12 lg:p-16 text-black">

                            <div class="flex items-center mb-6 pb-1">
                                <img src="https://media.licdn.com/dms/image/v2/D560BAQE_PPaNKlH0zg/company-logo_200_200/company-logo_200_200/0/1708059319694/kodewave_sdn_bhd_logo?e=2147483647&v=beta&t=_maEGEZxWE1SwFSQxlnNNggaxO3iW8UzEHSZiHSAsSI" alt="SmartFin360 Logo" class="h-12 w-auto">
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