<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Expense Approvals') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Review and manage pending and approved expense requests
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Expenses -->
                <div class="p-5 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-500 bg-opacity-20">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses Submitted</p>
                            <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                                RM{{ number_format($totalExpenses, 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="p-5 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                            <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                                {{ $totalPending }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="p-5 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</p>
                            <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                                {{ $totalApproved }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="p-5 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-500 bg-opacity-20">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</p>
                            <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                                {{ $totalRejected }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Expenses Section -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 dark:bg-yellow-900/40 p-3 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Pending Expenses — <span class="text-indigo-500">{{ Auth::user()->department->name ?? 'Unknown' }}</span>
                        </h2>
                    </div>
                </div>

                <!-- Alerts -->
                @foreach (['success' => 'green', 'error' => 'red', 'info' => 'blue'] as $msg => $color)
                    @if (session($msg))
                        <div class="mb-4 p-3 rounded-md bg-{{ $color }}-100 dark:bg-{{ $color }}-900 text-{{ $color }}-800 dark:text-{{ $color }}-200">
                            {{ session($msg) }}
                        </div>
                    @endif
                @endforeach

                @if ($pendingExpenses->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-10">
                        🎉 No pending expenses for approval.
                    </p>
                @else
                    <div class="overflow-x-auto overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Submitted By
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach ($pendingExpenses as $expense)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                        <!-- Submitted By -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $expense->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $expense->user->department->name ?? 'N/A' }}
                                            </div>
                                        </td>

                                        <!-- Category -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ ucfirst($expense->category) }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $expense->budget->name ?? 'No Budget' }}
                                            </div>
                                        </td>

                                        <!-- Description -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2">
                                                {{ $expense->description }}
                                            </div>
                                            @if($expense->receipt_path)
                                                <span class="inline-flex items-center mt-1 text-xs text-indigo-600 dark:text-indigo-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Receipt attached
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Amount -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                RM{{ number_format($expense->amount, 2) }}
                                            </span>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($expense->transaction_date)->format('M d, Y') }}
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusIcons = [
                                                    'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    'approved' => 'M5 13l4 4L19 7',
                                                    'rejected' => 'M6 18L18 6M6 6l12 12'
                                                ];
                                                
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                                ][$expense->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                            @endphp

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors }}">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$expense->status] ?? '' }}"></path>
                                                </svg>
                                                {{ ucfirst($expense->status) }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <!-- View Button -->
                                                <button data-expense-id="{{ $expense->id }}"
                                                    data-expense='{
                                                        "id": {{ $expense->id }},
                                                        "amount": {{ $expense->amount }},
                                                        "category": "{{ $expense->category }}",
                                                        "description": "{{ addslashes($expense->description) }}",
                                                        "receipt_path": "{{ $expense->receipt_path }}",
                                                        "receipt_url": "{{ $expense->receipt_path ? Storage::url($expense->receipt_path) : '' }}",
                                                        "status": "{{ $expense->status }}",
                                                        "created_at": "{{ $expense->created_at }}",
                                                        "rejection_reason": "{{ $expense->rejection_reason ? addslashes($expense->rejection_reason) : '' }}",
                                                        "user_name": "{{ $expense->user->name }}",
                                                        "budget": { "name": "{{ $expense->budget ? addslashes($expense->budget->name) : 'N/A' }}" },
                                                        "transaction_date": "{{ $expense->transaction_date }}",
                                                        "user": { "name": "{{ $expense->user->name }}" },
                                                        "processed_at": "{{ $expense->processed_at }}",
                                                        "processed_by": "{{ $expense->processedBy->name ?? 'System' }}"
                                                    }'
                                                    onclick="openDetailsModal({{ $expense->id }}, JSON.parse(this.getAttribute('data-expense')))" 
                                                    class="p-1.5 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-full hover:bg-indigo-50 dark:hover:bg-gray-700"
                                                    title="View Details">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Approve Button -->
                                                <button type="button" 
                                                        onclick="confirmAction('{{ route('expenses.approve', $expense->id) }}', 'approve')" 
                                                        class="p-1.5 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 rounded-full hover:bg-green-50 dark:hover:bg-gray-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>

                                                <!-- Reject Button -->
                                                <button type="button" 
                                                        onclick="confirmAction('{{ route('expenses.reject', $expense->id) }}', 'reject')" 
                                                        class="p-1.5 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 rounded-full hover:bg-red-50 dark:hover:bg-gray-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    

                    <div class="mt-5">
                        {{ $pendingExpenses->links() }}
                    </div>
                @endif
            </div>
            <!-- Approved Expenses Section -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 mt-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 dark:bg-green-900/40 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-gray-100">Approved Expenses</h2>
                    </div>
                </div>

                @if(isset($approvedExpenses) && $approvedExpenses->count() > 0)
                <div class="overflow-x-auto overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Submitted By
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Category
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Description
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($approvedExpenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <!-- Submitted By -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $expense->user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $expense->user->department->name ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($expense->category) }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $expense->budget->name ?? 'No Budget' }}
                                        </div>
                                    </td>

                                    <!-- Description -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2">
                                            {{ $expense->description }}
                                        </div>
                                        @if($expense->receipt_path)
                                            <span class="inline-flex items-center mt-1 text-xs text-indigo-600 dark:text-indigo-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Receipt attached
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                            RM{{ number_format($expense->amount, 2) }}
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($expense->transaction_date)->format('M d, Y') }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Approved
                                        </span>
                                        @if($expense->processed_at)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($expense->processed_at)->diffForHumans() }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- View Button -->
                                            <button data-expense-id="{{ $expense->id }}"
                                                    data-expense='{
                                                        "id": {{ $expense->id }},
                                                        "amount": {{ $expense->amount }},
                                                        "category": "{{ $expense->category }}",
                                                        "description": "{{ addslashes($expense->description) }}",
                                                        "receipt_path": "{{ $expense->receipt_path }}",
                                                        "receipt_url": "{{ $expense->receipt_path ? Storage::url($expense->receipt_path) : '' }}",
                                                        "status": "{{ $expense->status }}",
                                                        "created_at": "{{ $expense->created_at }}",
                                                        "rejection_reason": "{{ $expense->rejection_reason ? addslashes($expense->rejection_reason) : '' }}",
                                                        "user_name": "{{ $expense->user->name }}",
                                                        "budget": { "name": "{{ $expense->budget ? addslashes($expense->budget->name) : 'N/A' }}" },
                                                        "transaction_date": "{{ $expense->transaction_date }}",
                                                        "user": { "name": "{{ $expense->user->name }}" },
                                                        "processed_at": "{{ $expense->processed_at }}",
                                                        "processed_by": "{{ $expense->processedBy->name ?? 'System' }}"
                                                    }'
                                                    onclick="openDetailsModal({{ $expense->id }}, JSON.parse(this.getAttribute('data-expense')))" 
                                                    class="p-1.5 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-full hover:bg-indigo-50 dark:hover:bg-gray-700"
                                                    title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $approvedExpenses->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                    No approved expenses to display.
                </p>
            @endif
        </div>
        </div>
    </div>
    <!-- Modals -->
    <x-confirmation-modal />
    
    @foreach($pendingExpenses as $expense)
        <x-expense-details-modal :id="'expense-details-' . $expense->id" />
    @endforeach
    
    @foreach($approvedExpenses as $expense)
        <x-expense-details-modal :id="'expense-details-' . $expense->id" />
    @endforeach

    @push('scripts')
    <script>
        // JavaScript for both confirmation and details modals
        function confirmAction(url, action, modalId = 'confirmationModal', extraText = '') {
            const modal = document.getElementById(modalId);
            const card = document.getElementById(modalId + 'Card');
            const icon = document.getElementById(modalId + 'Icon');
            const title = document.getElementById(modalId + 'Title');
            const message = document.getElementById(modalId + 'Message');
            const confirmButton = document.getElementById(modalId + 'ConfirmButton');
            const form = document.getElementById(modalId + 'Form');
            const methodInput = document.getElementById(modalId + 'Method');
    
                form.action = url;
                form.method = 'POST'; // This is the actual form submission method
                methodInput.innerHTML = '<input type="hidden" name="_method" value="PATCH">'; // This tells Laravel to treat it as PATCH
                methodInput.innerHTML += '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') + '">';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);

            if (action === 'approve') {
                icon.innerHTML = `
                    <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 p-4 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>`;
                title.textContent = 'Approve Action?';
                message.textContent = 'Confirm approval ' + (extraText ? `(${extraText})` : '') + '?';
                confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:ring-green-300 transition';
            } else {
                icon.innerHTML = `
                    <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 p-4 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                        </svg>
                    </div>`;
                title.textContent = 'Reject Action?';
                message.textContent = 'Reject this item ' + (extraText ? `(${extraText})` : '') + '?';
                confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:ring-red-300 transition';
            }
        }

        function closeModal(modalId = 'confirmationModal') {
            const modal = document.getElementById(modalId);
            const card = document.getElementById(modalId + 'Card');

            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 150);
        }

        function openDetailsModal(expenseId, expenseData) {
            const modalId = 'expense-details-' + expenseId;
            const modal = document.getElementById(modalId);
            const modalCard = document.getElementById(modalId + 'Card');
            const contentDiv = document.getElementById(modalId + 'Content');
            
            if (!modal || !modalCard || !contentDiv) return;

            // Format the date
            const formattedDate = new Date(expenseData.created_at).toLocaleString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Set the modal title
            const titleDiv = document.getElementById(modalId + 'Title');
            if (titleDiv) {
                titleDiv.textContent = 'Expense Details';
            }

            // Set the content
            contentDiv.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">$${parseFloat(expenseData.amount).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${expenseData.category.charAt(0).toUpperCase() + expenseData.category.slice(1)}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted By</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${expenseData.user_name || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${formattedDate}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-4">Description</p>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${expenseData.description || 'No description provided'}</p>
                </div>

                ${expenseData.receipt_path ? `
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Receipt</p>
                    <a href="${expenseData.receipt_url}" target="_blank" class="mt-1 text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center">
                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Receipt
                    </a>
                </div>` : ''}

                ${expenseData.rejection_reason ? `
                <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-md">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">Rejection Reason</p>
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">${expenseData.rejection_reason}</p>
                </div>` : ''}
            `;

            // Show modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalCard.classList.remove('scale-95', 'opacity-0');
                modalCard.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDetailsModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalCard = document.getElementById(modalId + 'Card');
            
            if (!modal || !modalCard) return;
            
            // Hide modal with animation
            modalCard.classList.remove('scale-100', 'opacity-100');
            modalCard.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const confirmationModal = document.getElementById('confirmationModal');
                const detailsModal = document.getElementById('expenseDetailsModal');
                if (confirmationModal && !confirmationModal.classList.contains('hidden')) closeModal('confirmationModal');
            
            // Close any open expense detail modals
            document.querySelectorAll('[id^="expense-details-"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    closeDetailsModal(modal.id);
                }
            });
            }
        });
    </script>
    @endpush

</x-app-layout>
