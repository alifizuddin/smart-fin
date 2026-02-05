<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('My Spending Analytics') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Overview of your financial activities and key metrics
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <button 
                    wire:click="resetFilters"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filters
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Spent Card -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Approved</p>
                            <p class="text-2xl font-bold mt-1">RM {{ number_format($totalSpent, 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-blue-800 dark:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Card -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/30 text-yellow-800 dark:text-yellow-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-yellow-700 dark:text-yellow-400">Pending Approval</p>
                            <p class="text-2xl font-bold mt-1">RM {{ number_format($pendingAmount, 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-yellow-800 dark:text-yellow-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend Card -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-green-700 dark:text-green-400">This Month</p>
                            <div class="flex items-baseline space-x-2">
                                <p class="text-2xl font-bold">RM {{ number_format($thisMonth, 2) }}</p>
                                <span class="text-xs font-medium flex items-center">
                                    {{ $trend > 0 ? '+' : '' }}{{ number_format($trend, 1) }}%
                                    @if($trend >= 0)
                                        <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Spending Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Spending Trend</h3>
                        <div class="flex space-x-2">
                            <button wire:click="$set('dateRange', '1M')" class="px-3 py-1 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">1M</button>
                            <button wire:click="$set('dateRange', '3M')" class="px-3 py-1 text-xs rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">3M</button>
                            <button wire:click="$set('dateRange', '6M')" class="px-3 py-1 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">6M</button>
                            <button wire:click="$set('dateRange', '1Y')" class="px-3 py-1 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">1Y</button>
                        </div>
                    </div>
                    <div class="h-72">
                         <livewire:staff.components.spending-chart />
                    </div>
                </div>

                <!-- Category Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Spending by Category</h3>
                        <button class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            View Details
                        </button>
                    </div>
                    <div class="h-72 flex justify-center">
                        <livewire:staff.components.category-chart />
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl relative">
                <!-- Loading Overlay -->
                <div wire:loading.delay.shorter wire:target="filter" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm z-10 flex items-center justify-center transition-all duration-300">
                    <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 transform scale-100">
                        <div class="relative">
                            <svg class="animate-spin h-10 w-10 text-indigo-600 dark:text-indigo-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <div class="absolute inset-0 animate-ping">
                                <svg class="h-10 w-10 text-indigo-400 dark:text-indigo-300 mx-auto opacity-30" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Updating transactions...</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Please wait a moment</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                            <div wire:loading.delay.shorter wire:target="filter" class="ml-3 flex items-center space-x-2">
                                <svg class="animate-spin h-4 w-4 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Updating...</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                wire:click="$set('filter', 'all')" 
                                @class([
                                    'px-3 py-1 text-sm rounded-lg transition-all duration-200 relative',
                                    'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' => $filter === 'all',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' => $filter !== 'all',
                                    'opacity-70 cursor-not-allowed' => $this->isFiltering()
                                ])
                                wire:loading.attr="disabled"
                                wire:target="filter"
                            >
                                <span wire:loading.remove wire:target="filter">All</span>
                                <span wire:loading wire:target="filter" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </button>
                            <button 
                                wire:click="$set('filter', 'approved')" 
                                @class([
                                    'px-3 py-1 text-sm rounded-lg transition-all duration-200 relative',
                                    'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' => $filter === 'approved',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' => $filter !== 'approved',
                                    'opacity-70 cursor-not-allowed' => $this->isFiltering()
                                ])
                                wire:loading.attr="disabled"
                                wire:target="filter"
                            >
                                <span wire:loading.remove wire:target="filter">Approved</span>
                                <span wire:loading wire:target="filter" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </button>
                            <button 
                                wire:click="$set('filter', 'pending')" 
                                @class([
                                    'px-3 py-1 text-sm rounded-lg transition-all duration-200 relative',
                                    'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300' => $filter === 'pending',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' => $filter !== 'pending',
                                    'opacity-70 cursor-not-allowed' => $this->isFiltering()
                                ])
                                wire:loading.attr="disabled"
                                wire:target="filter"
                            >
                                <span wire:loading.remove wire:target="filter">Pending</span>
                                <span wire:loading wire:target="filter" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </button>
                            <button 
                                wire:click="$set('filter', 'rejected')" 
                                @class([
                                    'px-3 py-1 text-sm rounded-lg transition-all duration-200 relative',
                                    'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300' => $filter === 'rejected',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' => $filter !== 'rejected',
                                    'opacity-70 cursor-not-allowed' => $this->isFiltering()
                                ])
                                wire:loading.attr="disabled"
                                wire:target="filter"
                            >
                                <span wire:loading.remove wire:target="filter">Rejected</span>
                                <span wire:loading wire:target="filter" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentExpenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $expense->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $expense->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                        {{Str::limit($expense->description, 30)}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-white">
                                        RM {{ number_format($expense->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $expense->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 
                                              ($expense->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300') }}">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Controls -->
                    <div class="mt-6 w-full">
                        {{ $recentExpenses->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

