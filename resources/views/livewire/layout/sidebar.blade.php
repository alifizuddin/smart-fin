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

<div 
    class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 ease-in-out overflow-hidden"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center" wire:navigate>
                <div class="flex items-center justify-center h-9 w-9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-800 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <span x-show="sidebarOpen" class="ml-2 text-xl font-semibold text-gray-800 dark:text-white">SmartFin360</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="p-1 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
                <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-4">
            <div class="space-y-1 px-2">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('Dashboard') }}</span>
                </x-sidebar-link>

                <div x-data="{ budgetsDropdownOpen: false }" class="space-y-1">
                    <button @click="budgetsDropdownOpen = !budgetsDropdownOpen" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-r-md transition-colors duration-200">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">{{ __('Budgets') }}</span>
                        <svg x-show="sidebarOpen" :class="{'transform rotate-180': budgetsDropdownOpen}" class="ml-2 h-5 w-5 transition-transform duration-200 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div x-show="budgetsDropdownOpen && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="space-y-1">
                        @hasrole('Admin')
                        <x-sidebar-link :href="route('budgets.create')" :active="request()->routeIs('budgets.create')">
                            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-3">{{ __('Create Budget') }}</span>
                        </x-sidebar-link>
                        @endhasrole
                        <x-sidebar-link :href="route('budgets.index')" :active="request()->routeIs('budgets.index')">
                            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span class="ml-3">{{ __('View All Budgets') }}</span>
                        </x-sidebar-link>
                    </div>
                </div>

                @role('Staff')
                <x-sidebar-link :href="route('expenses.create')" :active="request()->routeIs('expenses.create')">
                    <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('Submit Expense') }}</span>
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('expenses.my-expenses')" :active="request()->routeIs('expenses.my-expenses')">
                    <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('My Expenses') }}</span>
                </x-sidebar-link>

                <x-sidebar-link :href="route('staff.analytics')" :active="request()->routeIs('staff.analytics')">
                    <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('My Analytics') }}</span>
                </x-sidebar-link>
                @endrole

                @role('Manager')
                <x-sidebar-link :href="route('expenses.approvals.index')" :active="request()->routeIs('expenses.approvals.index')">
                    <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('Approvals') }}</span>
                </x-sidebar-link>
                @endrole

                @role('Admin')
                <!-- User Management Dropdown -->
                <div x-data="{ userDropdownOpen: false }" class="space-y-1">
                    <button @click="userDropdownOpen = !userDropdownOpen" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-r-md transition-colors duration-200">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">{{ __('User Management') }}</span>
                        <svg x-show="sidebarOpen" :class="{'transform rotate-180': userDropdownOpen}" class="ml-2 h-5 w-5 transition-transform duration-200 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div x-show="userDropdownOpen && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="space-y-1">
                        <x-sidebar-link :href="route('users.create')" :active="request()->routeIs('users.create')">
                            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-3">{{ __('Add User') }}</span>
                        </x-sidebar-link>
                        <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span class="ml-3">{{ __('View All Users') }}</span>
                        </x-sidebar-link>
                    </div>
                </div>

                <!-- Department Management Dropdown -->
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-r-md transition-colors duration-200">
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">{{ __('Departments') }}</span>
                        <svg x-show="sidebarOpen" :class="{'transform rotate-180': open}" class="ml-2 h-5 w-5 transition-transform duration-200 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="space-y-1">
                        <x-sidebar-link :href="route('departments.create')" :active="request()->routeIs('departments.create')">
                            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-3">{{ __('Add Department') }}</span>
                        </x-sidebar-link>
                        <x-sidebar-link :href="route('departments.index')" :active="request()->routeIs('departments.index')">
                            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span class="ml-3">{{ __('View All') }}</span>
                        </x-sidebar-link>
                    </div>
                </div>

                @endrole

                @role('Finance Admin')
                <x-sidebar-link :href="route('reporting.index')" :active="request()->routeIs('reporting.*')">
                    <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('Reporting') }}</span>
                </x-sidebar-link>
                @endrole

                @role('Finance Admin|Manager')
                <x-sidebar-link :href="route('forecasting.index')" :active="request()->routeIs('forecasting.*')">
                     <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">{{ __('Forecasting') }}</span>
                </x-sidebar-link>
                @endrole
            </div>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <img class="h-10 w-10 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                <div x-show="sidebarOpen" class="ml-3 overflow-hidden">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="mt-4 space-y-1">
                <a href="{{ route('profile') }}" class="flex items-center px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md" wire:navigate>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-2">{{ __('Profile') }}</span>
                </a>
                <a class="flex items-center w-full text-left px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md">
                    <livewire:layout.notifications />
                    <span x-show="sidebarOpen" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Notifications') }}</span>
                </a>
                <button 
                    @click="toggleTheme()"
                    class="flex items-center w-full text-left px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-2">{{ __('Toggle Theme') }}</span>
                </button>
                
                <button wire:click="logout" class="flex items-center w-full text-left px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-2">{{ __('Log Out') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

