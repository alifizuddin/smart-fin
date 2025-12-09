<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    /**
     * Toggle the sidebar.
     */
    #[On('toggle-sidebar')]
    public function toggleSidebar(): void
    {
        $this->dispatch('sidebar-toggled');
    }
};
?>

<div x-data="{ open: false, profileOpen: false }" @keydown.window.escape="open = false; profileOpen = false" class="lg:hidden">
    <!-- Mobile menu button -->
    <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center
        ">
            <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:text-gray-400 dark:hover:bg-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" x-bind:class="{ 'hidden': open, 'block': !open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="h-6 w-6" x-bind:class="{ 'hidden': !open, 'block': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="ml-4">
                <x-application-logo class="block h-9 w-auto text-gray-800 dark:text-gray-200" />
            </a>
        </div>
        
        <div class="ml-3">
            <livewire:layout.notifications />
        </div>

        <!-- Profile dropdown -->
        <div class="relative ml-3">
            <div>
                <button @click="profileOpen = !profileOpen" type="button" class="flex items-center max-w-xs rounded-full bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                </button>
            </div>

            <!-- Dropdown menu -->
            <div x-show="profileOpen" @click.away="profileOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">
                    {{ __('Profile') }}
                </a>
                <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">
                    {{ __('Log Out') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div x-show="open" @click.away="open = false" class="lg:hidden">
        <div class="space-y-1 px-2 pb-3 pt-2 bg-white dark:bg-gray-800">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')" wire:navigate>
                {{ __('Budgets') }}
            </x-responsive-nav-link>
            @role('Staff')
            <x-responsive-nav-link :href="route('expenses.create')" :active="request()->routeIs('expenses.*')">
                {{ __('Submit Expense') }}
            </x-responsive-nav-link>
            @endrole
            @role('Manager')
            <x-responsive-nav-link :href="route('expenses.approvals.index')" :active="request()->routeIs('expenses.approvals.*')">
                {{ __('Approvals') }}
            </x-responsive-nav-link>
            @endrole
            @role('Admin')
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                {{ __('User Management') }}
            </x-responsive-nav-link>
            @endrole
            @role('Admin|Finance Admin')
            <x-responsive-nav-link :href="route('reporting.index')" :active="request()->routeIs('reporting.*')" wire:navigate>
                {{ __('Reporting') }}
            </x-responsive-nav-link>
            @endrole
        </div>
    </div>
</div>