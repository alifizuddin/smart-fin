<?php

use Livewire\Volt\Component;
use Illuminate\Notifications\DatabaseNotification;

new class extends Component {
    public function getNotificationsProperty()
    {
        // Fetch latest 20 notifications
        return auth()->user()->notifications()->take(20)->get();
    }

    public function getUnreadCountProperty()
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }
};
?>

<div x-data="{ open: false }" class="relative">
    <button 
        @click="open = true" 
        class="relative flex items-center justify-center text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
        <span class="sr-only">View notifications</span>
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($this->unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-800"></span>
        @endif
    </button>

    <template x-teleport="body">
        <div 
            x-show="open" 
            class="relative z-[99]" 
            aria-labelledby="slide-over-title" 
            role="dialog" 
            aria-modal="true"
            style="display: none;"
        >
            <div 
                x-show="open"
                x-transition:enter="ease-in-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                @click="open = false"
            ></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <div 
                            x-show="open"
                            x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:enter-start="translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="pointer-events-auto w-screen max-w-md"
                        >
                            <div class="flex h-full flex-col bg-white dark:bg-gray-900 shadow-xl">
                                <div class="flex-1 overflow-y-auto py-6 px-4 sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="slide-over-title">Notifications</h2>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button @click="open = false" type="button" class="relative -m-2 p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                                                <span class="sr-only">Close panel</span>
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-8">
                                        <div class="flow-root">
                                            @if($this->notifications->isEmpty())
                                                <p class="text-center text-gray-500 dark:text-gray-400 py-4">No notifications yet.</p>
                                            @else
                                                <ul role="list" class="-my-6 divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($this->notifications as $notification)
                                                        <li class="py-6 {{ $notification->read_at ? 'opacity-50' : '' }}">
                                                            <div class="flex gap-4">
                                                                <div class="flex-1">
                                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                        {{ $notification->data['title'] ?? 'Notification' }}
                                                                    </p>
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                                        {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                                                </div>
                                                                @if(!$notification->read_at)
                                                                    <div class="flex-shrink-0">
                                                                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-indigo-600 hover:text-indigo-500 text-xs font-medium">
                                                                            Mark read
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-6 sm:px-6">
                                    @if($this->unreadCount > 0)
                                        <button wire:click="markAllRead" class="flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            Mark all as read
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>