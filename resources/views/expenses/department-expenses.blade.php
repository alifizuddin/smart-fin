<x-app-layout>
    <x-slot name="header">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Department Expenses') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    View department submitted expenses
                </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Department Expenses Card -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Department Expenses</p>
                            <p class="text-2xl font-bold mt-1">RM {{ number_format($totalExpenses, 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-blue-800 dark:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Approved Expenses Card -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-green-700 dark:text-green-400">Approved Expenses</p>
                            <p class="text-2xl font-bold mt-1">RM {{ number_format($totalApproved, 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-green-800 dark:text-green-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <form action="{{ route('expenses.department') }}" method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <label for="search" class="sr-only">Search expenses</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 sm:text-sm" placeholder="Search expenses...">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full sm:w-48">
                        <label for="status" class="sr-only">Status</label>
                        <select id="status" name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="flex space-x-2">
                        <div class="w-36">
                            <label for="start_date" class="sr-only">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                        <div class="w-36">
                            <label for="end_date" class="sr-only">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex-shrink-0">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full justify-center">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Expenses Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($expenses->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No expenses found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter to find what you're looking for.</p>
                        </div>
                    @else
                        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 sm:rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Staff</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Budget</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($expenses as $expense)
                                            <tr class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ \Carbon\Carbon::parse($expense->created_at)->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($expense->created_at)->format('h:i A') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                            <span class="text-indigo-600 dark:text-indigo-300 font-medium text-sm">{{ substr($expense->user->name, 0, 1) }}</span>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $expense->user->name }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->user->department->name ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $categoryColors = [
                                                            'Travel' => ['bg' => 'blue', 'text' => 'blue-800', 'dark_bg' => 'blue-900', 'dark_text' => 'blue-200'],
                                                            'Food & Drink' => ['bg' => 'green', 'text' => 'green-800', 'dark_bg' => 'green-900', 'dark_text' => 'green-200'],
                                                            'Office Supplies' => ['bg' => 'yellow', 'text' => 'yellow-800', 'dark_bg' => 'yellow-900', 'dark_text' => 'yellow-200'],
                                                            'default' => ['bg' => 'gray', 'text' => 'gray-800', 'dark_bg' => 'gray-700', 'dark_text' => 'gray-300']
                                                        ];
                                                        $category = $categoryColors[$expense->category] ?? $categoryColors['default'];
                                                    @endphp
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        bg-{{ $category['bg'] }}-100 text-{{ $category['text'] }} 
                                                        dark:bg-{{ $category['dark_bg'] }} dark:text-{{ $category['dark_text'] }}">
                                                        {{ $expense->category }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate group relative">
                                                        <span class="truncate">{{ $expense->description }}</span>
                                                        @if(strlen($expense->description) > 40)
                                                            <div class="absolute z-10 hidden group-hover:block w-64 p-2 mt-1 text-xs text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded shadow-lg border border-gray-200 dark:border-gray-700">
                                                                {{ $expense->description }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-white">{{ $expense->budget ? $expense->budget->name : 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusColors = [
                                                            'approved' => ['bg' => 'green', 'text' => 'green-800', 'dark_bg' => 'green-900', 'dark_text' => 'green-200'],
                                                            'rejected' => ['bg' => 'red', 'text' => 'red-800', 'dark_bg' => 'red-900', 'dark_text' => 'red-200'],
                                                            'pending' => ['bg' => 'yellow', 'text' => 'yellow-800', 'dark_bg' => 'yellow-900', 'dark_text' => 'yellow-200'],
                                                            'default' => ['bg' => 'gray', 'text' => 'gray-800', 'dark_bg' => 'gray-700', 'dark_text' => 'gray-300']
                                                        ];
                                                        $status = $statusColors[strtolower($expense->status)] ?? $statusColors['default'];
                                                    @endphp
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        bg-{{ $status['bg'] }}-100 text-{{ $status['text'] }} 
                                                        dark:bg-{{ $status['dark_bg'] }} dark:text-{{ $status['dark_text'] }}">
                                                        {{ ucfirst($expense->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">RM {{ number_format($expense->amount, 2) }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end space-x-2">
                                                        <a href="{{ route('expenses.show', $expense) }}" 
                                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-150"
                                                           title="View Details">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 dark:bg-gray-800">
                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                            <td colspan="6" class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 text-right">Total:</td>
                                            <td class="px-6 py-3 text-right">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">RM {{ number_format($expenses->sum('amount'), 2) }}</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $expenses->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Set default end date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            if (!document.getElementById('end_date').value) {
                document.getElementById('end_date').value = today;
            }
        });
    </script>
    @endpush
</x-app-layout>