<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: false);
    }
};
?>

<div x-data="{ open: window.innerWidth >= 1024 }" class="flex flex-col md:flex-row min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <div 
        :class="{'w-64': open, 'w-20': !open}"
        class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 ease-in-out overflow-hidden"
    >
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="flex items-center" wire:navigate>
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <span x-show="open" class="ml-2 text-xl font-semibold text-gray-800 dark:text-white">SmartFin360</span>
                </a>
                <button @click="open = !open" class="p-1 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="space-y-1 px-2">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span x-show="open" class="ml-3">{{ __('Dashboard') }}</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="open" class="ml-3">{{ __('Budgets') }}</span>
                    </x-sidebar-link>

                    @role('Staff')
                    <x-sidebar-link :href="route('expenses.create')" :active="request()->routeIs('expenses.*')">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span x-show="open" class="ml-3">{{ __('Submit Expense') }}</span>
                    </x-sidebar-link>
                    @endrole

                    @role('Manager')
                    <x-sidebar-link :href="route('expenses.approvals.index')" :active="request()->routeIs('expenses.approvals.index')">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="open" class="ml-3">{{ __('Approvals') }}</span>
                    </x-sidebar-link>
                    @endrole

                    @role('Admin')
                    <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="open" class="ml-3">{{ __('User Management') }}</span>
                    </x-sidebar-link>
                    @endrole
                </div>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <img class="h-10 w-10 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                    <div x-show="open" class="ml-3 overflow-hidden">
                        <div class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div class="mt-4 space-y-1">
                    <a href="{{ route('profile') }}" class="flex items-center px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span x-show="open" class="ml-2">{{ __('Profile') }}</span>
                    </a>
                    <button wire:click="logout" class="flex items-center w-full text-left px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span x-show="open" class="ml-2">{{ __('Log Out') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden pt-16 md:pt-0" :class="{'lg:ml-64': open, 'lg:ml-20': !open}">
        <!-- Top Navigation -->
        <header class="bg-white dark:bg-gray-800 shadow-sm z-40">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <button @click="open = !open" class="p-2 rounded-md text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 lg:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @if (isset($header))
                        <h1 class="text-lg font-medium text-gray-900 dark:text-gray-100 ml-4 lg:ml-0">
                            {{ $header }}
                        </h1>
                    @endif
                </div>
                
                <!-- Mobile profile dropdown -->
                <div class="lg:hidden">
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="flex items-center max-w-xs rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900">
            @if (isset($header))
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $header }}</h1>
                </div>
            @endif
            
            {{ $slot ?? '' }}
        </main>
    </div>
</div>
