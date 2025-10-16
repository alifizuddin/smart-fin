<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SmartFin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="relative min-h-screen flex flex-col items-center justify-center">

            <main class="flex items-center justify-center w-full">
                <div class="w-full max-w-5xl mx-auto p-4 flex flex-col md:flex-row bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <div class="hidden md:block md:w-1/2">
                        <img class="h-full w-full object-cover rounded-l-lg" src="https://images.unsplash.com/photo-1664575602276-acd073f104c1?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8YnVzaW5lc3N8ZW58MHx8MHx8fDA%3D&fm=jpg&q=60&w=3000" alt="Financial Planning Meeting">
                    </div>

                    <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-8">
                        <form class="w-full max-w-sm">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center">Sign In</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mt-2">Welcome! Please sign in to continue.</p>

                            <div class="mt-8">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div class="mt-6">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <div class="flex items-center">
                                    <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="remember_me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Remember me</label>
                                </div>
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Forgot password?</a>
                            </div>

                            <div class="mt-8">
                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Sign In
                                </button>
                            </div>

                            @if (Route::has('register'))
                                <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                                    Don’t have an account?
                                    <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Sign up
                                    </a>
                                </p>
                            @endif
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>