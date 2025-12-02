@php
    \Log::info('Rendering notifications', [
        'user_id' => auth()->id(),
        'notifications_count' => $this->notifications->count(),
        'unread_count' => $this->unreadCount
    ]);
@endphp

<?php

use Livewire\Volt\Component;
use Illuminate\Notifications\DatabaseNotification;

new class extends Component {

    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function getNotificationsProperty()
    {
        if (!auth()->check()) {
        return collect();
    }
    
    return auth()->user()
        ->notifications()
        ->latest()
        ->take(20)
        ->get()
        ->map(function ($notification) {
            // Ensure data is always an array
            $notification->data = is_array($notification->data) 
                ? $notification->data 
                : (array) $notification->data;
            return $notification;
        });
    }

    public function getUnreadCountProperty()
    {
        return auth()->user()->unreadNotifications()->count();
    }
    public function getListeners()
{
    return [
        "echo-private:App.Models.User." . auth()->id() . ",.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'refreshNotifications',
    ];
}

public function refreshNotifications()
{
    $this->notifications = $this->notifications;
    $this->unreadCount = $this->unreadCount;
}

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
    
    // Redirect to the notification's action URL if it exists
        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }
    
    // Otherwise, just refresh the notifications
        $this->dispatch('$refresh');
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
            <span class="absolute -top-0.5 -right-0.5 block h-2 w-2 rounded-full bg-red-500 ring-1 ring-white dark:ring-gray-800"></span>
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
                                                        @php
                                                        $isExpenseSubmission = $notification->type === 'App\\Notifications\\NewExpenseSubmission';
                                                        $isExpenseApproved = $notification->type === 'App\\Notifications\\ExpenseApproved';
                                                        $isExpenseNotification = $isExpenseSubmission || $isExpenseApproved;
                                                        
                                                        // Set the appropriate action URL based on notification type
                                                        if ($isExpenseSubmission) {
                                                            $actionUrl = $notification->data['action_url'] ?? route('expenses.approvals');
                                                        } elseif ($isExpenseApproved) {
                                                            $actionUrl = $notification->data['action_url'] ?? route('expenses.my-expenses');
                                                        } else {
                                                            $actionUrl = $notification->data['action_url'] ?? '#';
                                                        }
                                                    @endphp
                                                        <li class="py-4 {{ $notification->read_at ? 'opacity-50' : '' }}">
                                                            <a href="{{ $actionUrl }}" class="block hover:bg-gray-50 dark:hover:bg-gray-800 -mx-4 px-4 py-2 rounded-md transition-colors">
                                                                <div class="flex gap-4">
                                                                    <div class="flex-shrink-0">
                                                                        @if($isExpenseNotification)
                                                                            @if($isExpenseApproved)
                                                                                <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                                                    <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                    </svg>
                                                                                </div>
                                                                            @else
                                                                                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                                                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                                                                    </svg>
                                                                                </div>
                                                                            @endif
                                                                        @else
                                                                            <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                                                <svg class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-1 min-w-0 flex items-start justify-between gap-2">
                                                                        <div>
                                                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                                {{ $notification->data['title'] ?? ($isExpenseApproved ? 'Expense Approved' : 'New Expense Submission') }}
                                                                            </p>
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                                                {{ $notification->data['message'] ?? ($isExpenseApproved ? 'Your expense has been approved' : 'A new expense requires your approval') }}
                                                                            </p>
                                                                            <p class="mt-1 text-xs text-gray-400">
                                                                                {{ $notification->created_at->diffForHumans() }}
                                                                            </p>
                                                                        </div>
                                                                        @if(!$notification->read_at)
                                                                            <span class="h-2.5 w-2.5 rounded-full bg-indigo-600 flex-shrink-0 mt-1"></span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </a>
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
<script>
    document.addEventListener('livewire:initialized', () => {
        // Mark notification as read when clicked
        document.querySelectorAll('a[href^="{{ route('expenses.approvals.index') }}"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const notificationId = this.closest('li').getAttribute('data-notification-id');
                if (notificationId) {
                    @this.markAsRead(notificationId);
                }
            });
        });
    });
</script>