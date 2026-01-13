<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('My Expenses') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    View and manage all your submitted expenses
                </p>
            </div>
            <a href="{{ route('expenses.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('New Expense') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
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
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</p>
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <form action="{{ route('expenses.my-expenses') }}" method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="search" 
                                           id="search"
                                           value="{{ request('search') }}" 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           placeholder="Search by category, description, or status...">
                                </div>
                            </div>
                            <div class="w-full sm:w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select name="status" 
                                        id="status"
                                        onchange="this.form.submit()" 
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Expenses Table -->
                    <div class="overflow-hidden overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        @if($expenses->isEmpty())
                            <div class="text-center py-12 px-4 sm:px-6 lg:px-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No expenses</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Get started by creating a new expense.
                                </p>
                                <div class="mt-6">
                                    <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        New Expense
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="align-middle inline-block min-w-full">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date
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
                                        @foreach($expenses as $expense)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                                <!-- Date -->
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        {{ \Carbon\Carbon::parse($expense->created_at)->format('M d, Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($expense->created_at)->format('h:i A') }}
                                                    </div>
                                                </td>

                                                <!-- Category -->
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $expense->category }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $expense->department->name ?? 'N/A' }}
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
                                                        {{ $expense->budget->name ?? 'No Budget' }}
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
                                                        
                                                        $statusIcons = array_merge([
                                                            'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                                            'approved' => 'M5 13l4 4L19 7',
                                                            'rejected' => 'M6 18L18 6M6 6l12 12'
                                                        ], $statusIcons);
                                                        
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                                        ][$expense->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                                    @endphp

                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors }}">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$expense->status] ?? '' }}"></path>
                                                        </svg>
                                                        {{ ucfirst($expense->status) }}
                                                    </span>
                                                    
                                                    @if($expense->status !== 'pending' && $expense->processed_at)
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
                                                                    "rejection_reason": "{{ $expense->rejection_reason ? addslashes($expense->rejection_reason) : '' }}"
                                                                }'
                                                                class="p-1.5 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-full hover:bg-indigo-50 dark:hover:bg-gray-700">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>

                                                        <!-- Edit Button (only for pending expenses) -->
                                                        @if($expense->status === 'pending')

                                                            <!-- Delete Button with Confirmation Modal -->
                                                            <button data-delete-url="{{ route('expenses.destroy', $expense->id) }}" 
                                                                    class="p-1.5 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 rounded-full hover:bg-red-50 dark:hover:bg-gray-700"
                                                                    title="Delete Expense">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Expense Details Modal Component -->
                                            <x-expense-details-modal :id="'expense-details-' . $expense->id" />


                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            @if($expenses->hasPages())
                                <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
                                    <div class="flex-1 flex justify-between sm:hidden">
                                        @if($expenses->onFirstPage())
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 cursor-not-allowed">
                                                Previous
                                            </span>
                                        @else
                                            <a href="{{ $expenses->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                Previous
                                            </a>
                                        @endif
                                        
                                        @if($expenses->hasMorePages())
                                            <a href="{{ $expenses->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                Next
                                            </a>
                                        @else
                                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 cursor-not-allowed">
                                                Next
                                            </span>
                                        @endif
                                    </div>
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                Showing <span class="font-medium">{{ $expenses->firstItem() }}</span>
                                                to <span class="font-medium">{{ $expenses->lastItem() }}</span>
                                                of <span class="font-medium">{{ $expenses->total() }}</span> results
                                            </p>
                                        </div>
                                        <div>
                                            {{ $expenses->withQueryString()->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single Confirmation Modal --}}
    <x-confirmation-modal 
        id="deleteConfirmationModal" 
        title="Delete Expense" 
        message="Are you sure you want to delete this expense? This action cannot be undone."
    />

    @push('scripts')
<script>
    // Function to show delete confirmation modal
    function showDeleteConfirmation(actionUrl) {
        const modalId = 'deleteConfirmationModal';
        const modal = document.getElementById(modalId);
        const modalCard = document.getElementById(`${modalId}Card`);
        const form = document.getElementById(`${modalId}Form`);
        const methodDiv = document.getElementById(`${modalId}Method`);
        const iconDiv = document.getElementById(`${modalId}Icon`);
        const confirmBtn = document.getElementById(`${modalId}ConfirmButton`);
        
        if (!modal || !modalCard || !form) return;
        
        // Setup Form
        form.action = actionUrl;
        
        // Set Method to DELETE
        if (methodDiv) {
            methodDiv.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
        }

        // Setup Icon (Red Trash)
        if (iconDiv) {
            iconDiv.className = "w-16 h-16 flex items-center justify-center mx-auto rounded-full mb-4 bg-red-100 dark:bg-red-900/30";
            iconDiv.innerHTML = `
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            `;
        }

        // Style Confirm Button
        if (confirmBtn) {
            confirmBtn.className = "px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-medium";
            confirmBtn.textContent = 'Delete';
        }

        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalCard.classList.remove('scale-95', 'opacity-0');
            modalCard.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Function to open the details modal
    function openDetailsModal(expenseId, expenseData) {
        const modalId = `expense-details-${expenseId}`;
        const modal = document.getElementById(modalId);
        const modalCard = document.getElementById(`${modalId}Card`);
        
        if (!modal || !modalCard) return;

        // Set the content dynamically
        const contentDiv = document.getElementById(`${modalId}Content`);
        if (contentDiv) {
            contentDiv.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">RM${parseFloat(expenseData.amount).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${expenseData.category.charAt(0).toUpperCase() + expenseData.category.slice(1)}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</p>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${expenseData.description || 'No description'}</p>
                </div>

                ${expenseData.receipt_path ? `
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Receipt</p>
                    <a href="${expenseData.receipt_url}" target="_blank" class="mt-1 text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center">
                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Receipt
                    </a>
                </div>` : ''}

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                            ${new Date(expenseData.created_at).toLocaleString('en-US', { 
                                month: 'short', 
                                day: 'numeric', 
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true 
                            })}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(expenseData.status)}">
                            ${expenseData.status.charAt(0).toUpperCase() + expenseData.status.slice(1)}
                        </span>
                    </div>
                </div>

                ${expenseData.status === 'rejected' && expenseData.rejection_reason ? `
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</p>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">${expenseData.rejection_reason}</p>
                </div>` : ''}
            `;
        }

        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalCard.classList.remove('scale-95', 'opacity-0');
            modalCard.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Function to close any modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const modalCard = document.getElementById(`${modalId}Card`);
        
        if (!modal || !modalCard) return;
        
        // Close modal with animation
        modalCard.classList.remove('scale-100', 'opacity-100');
        modalCard.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Helper function to get status color classes
    function getStatusColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'approved': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        };
        return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    // Event delegation
    document.addEventListener('click', function(e) {
        // Handle view button clicks
        const viewButton = e.target.closest('button[data-expense-id]');
        if (viewButton) {
            e.preventDefault();
            const expenseId = viewButton.getAttribute('data-expense-id');
            const expenseData = JSON.parse(viewButton.getAttribute('data-expense'));
            openDetailsModal(expenseId, expenseData);
            return;
        }
        
        // Handle delete button clicks
        const deleteButton = e.target.closest('button[data-delete-url]');
        if (deleteButton) {
            e.preventDefault();
            const url = deleteButton.getAttribute('data-delete-url');
            showDeleteConfirmation(url);
            return;
        }
        
        // Handle close button clicks
        const closeButton = e.target.closest('button[onclick^="closeModal"]');
        if (closeButton) {
            e.preventDefault();
            const modalId = closeButton.getAttribute('onclick').match(/'([^']+)'/)[1];
            closeModal(modalId);
            return;
        }
        
        // Close modal when clicking outside
        if (e.target.classList.contains('backdrop-blur-sm')) {
            closeModal(e.target.id);
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.backdrop-blur-sm:not(.hidden)');
            openModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
</script>
@endpush
</x-app-layout>