<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @hasrole('Staff')
    <main
        x-data="dashboardComponent('{{ route('expenses.quickStore') }}', '{{ csrf_token() }}')"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8"
    >

        <!-- GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Quick Action -->   

            <!-- My Expense Submissions -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 dark:bg-green-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">My Expense Submissions</h2>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Category</th>
                                <th class="px-5 py-3 text-left font-semibold">Amount</th>
                                <th class="px-5 py-3 text-left font-semibold">Status</th>
                                <th class="px-5 py-3 text-left font-semibold">Date Submitted</th>
                                <th class="px-5 py-3 text-left font-semibold">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($userExpenses as $expense)
                                <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                    <!-- Category -->
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                        {{ $expense->category }}
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                        ${{ number_format($expense->amount, 2) }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-5 py-3">
                                        @php
                                            $statusColors = [
                                                'pending'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                            ];
                                        @endphp

                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm 
                                            {{ $statusColors[$expense->status] ?? 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $expense->created_at->format('M d, Y') }}
                                    </td>

                                    <!-- Action (Delete icon button) -->
                                    <td class="px-5 py-3">
                                        <form action="{{ route('expenses.delete', $expense->id) }}" 
                                            method="POST"
                                            onsubmit="event.preventDefault(); confirmAction('{{ route('expenses.delete', $expense->id) }}', 'delete', 'confirmationModal', 'expense #${{ number_format($expense->amount, 2) }}');"
                                            class="inline-block">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="w-9 h-9 flex items-center justify-center rounded-full 
                                                    bg-red-100 text-red-700 hover:bg-red-200 
                                                    dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 
                                                    transition shadow-sm">

                                                <!-- Trash Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-9 0h12" />
                                                </svg>

                                            </button>

                                        </form>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                        You have no expense submissions yet.
                                    </td>
                                @endforelse
                            </tbody>
                        </table>

                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 dark:bg-blue-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Action</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">Submit a new expense in seconds.</p>

                <button
                    type="button"
                    @click="showModal = true"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-md font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700 transition"
                >
                    Submit Expense
                </button>
            </div>

            <!-- AI Suggestions -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-100 dark:bg-orange-900/40 p-3 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">AI Suggestions</h2>
                </div>

                <div class="text-sm text-gray-800 dark:text-gray-200 bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-md shadow-sm border border-yellow-100 dark:border-yellow-800/50">
                    @forelse($aiSuggestions ?? [] as $suggestion)
                        <p class="leading-relaxed font-medium mb-2 last:mb-0">
                            {{ $suggestion }}
                        </p>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 italic">No suggestions available.</p>
                    @endforelse
                </div>
            </div>


            <!-- Budget Overview -->
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 dark:border-gray-700 relative overflow-hidden md:col-span-2 lg:col-span-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-purple-100/30 to-transparent dark:from-purple-900/20 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex items-center mb-6">
                        <div class="bg-purple-100 dark:bg-purple-900/40 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Budget Overview</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Track your spending and remaining balance</p>
                        </div>
                    </div>

                <!-- Budget Selector Dropdown -->
                @if($userBudgets->count() > 1)
                <div class="mb-6">
                    <form action="{{ route('dashboard') }}" method="GET" id="budgetSelectorForm">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Budget</label>
                        <select 
                            name="budget_id" 
                            class="w-full bg-white/80 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent block p-3 dark:bg-gray-700/80 dark:placeholder-gray-400 dark:text-white shadow-sm transition-all duration-200"
                            onchange="document.getElementById('budgetSelectorForm').submit()"
                        >
                            @foreach($userBudgets as $budget)
                                <option value="{{ $budget->id }}" {{ $activeBudget && $activeBudget->id == $budget->id ? 'selected' : '' }}>
                                    {{ $budget->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif

                

                @if($activeBudget)
                    @php
                        // --- Data Calculation ---
                        $total = $activeBudget->total_amount;
                        $spent_approved = $userExpenses->where('status', 'approved')->sum('amount');
                        $spent_pending = $userExpenses->where('status', 'pending')->sum('amount');
                        
                        $effective_spent = $spent_approved;
                        $remaining = max($total - $effective_spent, 0);
                        
                        // Use effective_spent for the progress bar percentage
                        $percent = $total > 0 ? round(($effective_spent / $total) * 100, 2) : 0;

                        // --- Dynamic Progress Bar Colors ---
                        $progressBarColor = 'bg-green-600 dark:bg-green-400'; // Default (Green)
                        if ($percent >= 90) {
                            $progressBarColor = 'bg-red-600 dark:bg-red-400'; // Danger (Red)
                        } elseif ($percent >= 75) {
                            $progressBarColor = 'bg-yellow-500 dark:bg-yellow-400'; // Warning (Yellow)
                        }
                    @endphp

                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <!-- Total Budget Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Budget</p>
                                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">${{ number_format($total, 2) }}</p>
                                </div>
                                <div class="bg-blue-100 dark:bg-blue-800/50 p-3 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expenses Row -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800/30">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-red-600 dark:text-red-400">Approved</p>
                                        <p class="text-lg font-bold text-red-900 dark:text-red-100 mt-1">${{ number_format($spent_approved, 2) }}</p>
                                    </div>
                                    <div class="bg-red-100 dark:bg-red-800/50 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 p-4 rounded-xl border border-yellow-100 dark:border-yellow-800/30">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending</p>
                                        <p class="text-lg font-bold text-yellow-900 dark:text-yellow-100 mt-1">${{ number_format($spent_pending, 2) }}</p>
                                    </div>
                                    <div class="bg-yellow-100 dark:bg-yellow-800/50 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Remaining Balance Card -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Remaining Balance</p>
                                    <p class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">${{ number_format($remaining, 2) }}</p>
                                </div>
                                <div class="bg-green-100 dark:bg-green-800/50 p-3 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Budget Usage</span>
                            <span class="text-sm font-bold {{ $percent >= 90 ? 'text-red-600 dark:text-red-400' : ($percent >= 75 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">{{ $percent }}%</span>
                        </div>
                        <div class="relative">
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="h-3 rounded-full transition-all duration-1000 ease-out {{ $progressBarColor }} relative overflow-hidden" style="width: {{ $percent }}%">
                                    <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent"></div>
                                    <div class="absolute inset-0 animate-pulse bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Valid until {{ \Carbon\Carbon::parse($activeBudget->period_end)->format('M d, Y') }}
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($percent >= 90)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Critical
                                </span>
                            @elseif($percent >= 75)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Warning
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Healthy
                                </span>
                            @endif
                        </div>
                    </div>

                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                        You do not have an active budget at this time.
                    </p>
                @endif
            </div>
        </div>

            <!-- Quick Stats Dashboard -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
                <div class="flex items-center mb-4">
                    <div class="bg-teal-100 dark:bg-teal-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V9a2 2 0 012-2h2a2 2 0 012 2v10m6 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Stats</h2>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Average Daily Spending -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            @php
                                $avgDailySpending = $userExpenses->count() > 0 ? 
                                    round($userExpenses->where('status', 'approved')->sum('amount') / max(1, now()->diffInDays(\Carbon\Carbon::parse($activeBudget->period_start))), 2) : 
                                    0;
                            @endphp
                            ${{ number_format($avgDailySpending, 2) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Avg Daily</div>
                    </div>
                    
                    <!-- Monthly Comparison -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="text-2xl font-bold">
                            @php
                                $monthlyChange = 0; // Placeholder for monthly comparison logic
                                $changeColor = $monthlyChange >= 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400';
                                $changeIcon = $monthlyChange >= 0 ? '↑' : '↓';
                            @endphp
                            <span class="{{ $changeColor }}">
                                {{ $changeIcon }} {{ abs($monthlyChange) }}%
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">vs Last Month</div>
                    </div>
                    
                    <!-- Total Expenses -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $userExpenses->count() }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Expenses</div>
                    </div>
                    
                    <!-- Budget Utilization -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="text-2xl font-bold {{ $percent > 80 ? 'text-red-600 dark:text-red-400' : ($percent > 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">
                            {{ $percent }}%
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Budget Used</div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-100 dark:border-blue-800/40">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            Your spending is {{ $percent > 80 ? 'above' : ($percent > 60 ? 'moderate' : 'healthy') }} compared to your budget
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Expense Modal -->
        <template x-if="showModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Expense Submission</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in your expense details below. The system will automatically assign your expense to a budget with sufficient remaining balance.</p>

                    <!-- Alerts -->
                    <template x-if="successMessage">
                        <div class="mt-3 text-sm text-green-600 dark:text-green-400 p-3 bg-green-50 dark:bg-green-900/30 rounded-md border border-green-200 dark:border-green-700" x-text="successMessage"></div>
                    </template>
                    <template x-if="errorMessage">
                        <div class="mt-3 text-sm text-red-600 dark:text-red-400 p-3 bg-red-50 dark:bg-red-900/30 rounded-md border border-red-200 dark:border-red-700" x-text="errorMessage"></div>
                    </template>

                    <form @submit.prevent="handleSubmit" class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="amount" value="Amount ($)" />
                            <x-text-input id="amount" type="number" x-model="form.amount" step="0.01" required class="w-full mt-1" />
                        </div>

                        <div class="mb-6">
                            <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Upload Receipt
                            </label>
                            <input type="file" id="receipt" name="receipt" accept="image/*,.pdf"
                                @change="form.receipt = $event.target.files[0]"
                                class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-600 file:text-white
                                        hover:file:bg-indigo-700
                                        dark:file:bg-indigo-500 dark:hover:file:bg-indigo-600"
                                required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Accepted formats: JPG, PNG, or PDF (max 5MB)
                            </p>
                        </div>

                        <div>
                            <x-input-label for="description" value="Description (Optional)" />
                            <textarea id="description" x-model="form.description" rows="3" class="w-full mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="e.g. Team lunch at restaurant"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end items-center">
                            <x-secondary-button type="button" @click="showModal = false">
                                Cancel
                            </x-secondary-button>

                            <x-primary-button 
                                type="submit" 
                                class="ms-3" 
                                x-bind:disabled="loading"
                            >
                                <span x-show="!loading">Submit Expense</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting...
                                </span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </main>
    @endhasrole

    @hasrole('Manager')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Action Required: Approvals -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Action Required: Approvals</h2>
            </div>

            <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">
                Review and approve or reject expenses submitted by your department staff.
            </p>

            @if($pendingExpenses->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Budget Name</th>
                                <th class="px-5 py-3 text-left font-semibold">Staff</th>
                                <th class="px-5 py-3 text-left font-semibold">Amount</th>
                                <th class="px-5 py-3 text-left font-semibold">Category</th>
                                <th class="px-5 py-3 text-left font-semibold">Date Submitted</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($pendingExpenses as $expense)
                                <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                    <!-- Budget Name -->
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $expense->budget->name }}
                                    </td>

                                    <!-- Staff Name -->
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $expense->user->name }}
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                        ${{ number_format($expense->amount, 2) }}
                                    </td>

                                    <!-- Category Tag -->
                                    <td class="px-5 py-3">
                                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold
                                            bg-blue-100 text-blue-800
                                            dark:bg-blue-900/40 dark:text-blue-300 shadow-sm">
                                            {{ $expense->category }}
                                        </span>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($expense->created_at)->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            @else
                <div class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
                    No pending expenses for your department.
                </div>
            @endif
        </div>

        <!-- AI Alerts -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 dark:bg-orange-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">AI Alerts</h2>
            </div>

            <ul class="space-y-2">
                @foreach($anomalyAlerts as $alert)
                    <li class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 p-3 rounded-md border border-red-100 dark:border-red-800/40 shadow-sm">
                        {{ $alert['message'] ?? 'No message provided' }}
                    </li>
                @endforeach
                <li class="text-sm text-gray-500 dark:text-gray-400 italic">No current anomalies detected.</li>
            </ul>
        </div>

        <!-- AI Expense Forecasting -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 dark:bg-indigo-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">AI Forecasting</h2>
            </div>

            {{--<div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="flex justify-between">
                    <span>Next Month Prediction:</span>
                    <span class="font-semibold">${{ number_format($forecasting['next_month_prediction'], 2) }}</span>
                </div>
                <p class="italic">{{ $forecasting['trend'] }}</p>
            </div>--}}
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Forecasting data not available yet.</p>
        </div>

        <!-- Spending by Category Chart -->

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 dark:bg-purple-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V9a2 2 0 012-2h2a2 2 0 012 2v10m6 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Spending by Category</h2>
            </div>
            
            <canvas id="spendingChart" width="400" height="200"></canvas>
        </div>
    </div>
</main>
@endhasrole

@hasrole('Admin')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

    <!-- System Overview -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
        <div class="flex items-center mb-4">
            <div class="bg-blue-100 dark:bg-blue-900/40 p-3 rounded-full">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L12 19.25 14.25 17m-7.5-5.75h10.5M4.75 6.75h14.5c.69 0 1.25.56 1.25 1.25v10c0 .69-.56 1.25-1.25 1.25H4.75A1.25 1.25 0 013.5 18V8c0-.69.56-1.25 1.25-1.25z" />
                </svg>
            </div>
            <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">System Overview</h2>
        </div>

        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
            <strong>Total Users:</strong> 1,250 |
            <strong>Active Modules:</strong> 95% |
            <strong>Uptime:</strong> 99.9%
        </p>

        <canvas id="systemChart" class="w-full h-40"></canvas>
    </div>

    <!-- Alerts & Logs -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center mb-4">
            <div class="bg-orange-100 dark:bg-orange-900/40 p-3 rounded-full">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Alerts & Logs</h2>
        </div>

        <ul class="space-y-2 mb-3">
            <li class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 p-3 rounded-md border border-red-100 dark:border-red-800/40 shadow-sm">
                Anomaly: Unusual login from IP 192.168.1.1
            </li>
            <li class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 p-3 rounded-md border border-gray-200 dark:border-gray-600 shadow-sm">
                Log: System update completed
            </li>
        </ul>

        <button class="px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-yellow-400">
            View Full Logs
        </button>
    </div>

    <!-- Compliance Engine -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center mb-4">
            <div class="bg-indigo-100 dark:bg-indigo-900/40 p-3 rounded-full">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Compliance Engine</h2>
        </div>

        <p class="text-sm text-gray-700 dark:text-gray-300">
            <strong>Spending Limit per Category:</strong> $10,000
        </p>
        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
            <strong>Approved Vendors:</strong> Vendor A, Vendor B
        </p>

        <button class="mt-4 px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-blue-400">
            Edit Rules
        </button>
    </div>
</div>
</main>
@endhasrole

@hasrole('Finance Admin')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Budget Overview (Annual Financial Budget) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 dark:bg-green-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Budget Overview</h2>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                <strong>Total Annual Budget:</strong> $5,000,000 |
                <strong>Spent:</strong> $3,200,000 (64%) |
                <strong>Remaining:</strong> $1,800,000
            </p>
            <div class="relative w-full h-[260px]">
                <canvas id="budgetChart"></canvas>
            </div>
            <button class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-400">View Details</button>
        </div>

        <!-- Budget Alerts -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 dark:bg-red-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Budget Alerts</h2>
            </div>
            <ul class="space-y-2 mb-3">
                <li class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 p-3 rounded-md border border-red-100 dark:border-red-800/40 shadow-sm">
                    Alert: Marketing Dept budget at 95% limit ($950,000 spent).
                </li>
                <li class="text-sm text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-md border border-yellow-100 dark:border-yellow-800/40 shadow-sm">
                    Alert: IT Dept approaching 80% limit.
                </li>
            </ul>
            <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-400">Acknowledge Alerts</button>
        </div>

        <!-- Departmental Budget Approval -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Dept Budget Approval</h2>
            </div>
            <ul class="space-y-2 mb-3">
                <li class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 p-3 rounded-md border border-gray-200 dark:border-gray-600 shadow-sm">
                    Marketing: Proposed $1,000,000 - <button class="text-green-600 hover:underline">Approve</button> | <button class="text-red-600 hover:underline">Reject</button>
                </li>
                <li class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 p-3 rounded-md border border-gray-200 dark:border-gray-600 shadow-sm">
                    IT: Proposed $800,000 - <button class="text-green-600 hover:underline">Approve</button> | <button class="text-red-600 hover:underline">Reject</button>
                </li>
            </ul>
            <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400">Review All Proposals</button>
        </div>

        <!-- Fund Reallocation -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 dark:bg-purple-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Fund Reallocation</h2>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                <strong>Available for Reallocation:</strong> $500,000
            </p>
            <div class="space-y-2">
                <input type="number" placeholder="Amount to Transfer" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                <select class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                    <option>From: Marketing</option>
                    <option>From: IT</option>
                </select>
                <select class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                    <option>To: HR</option>
                    <option>To: Operations</option>
                </select>
            </div>
            <button class="mt-4 px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-400">Reallocate Funds</button>
        </div>

        <!-- Financial Reports & Analytics -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 dark:bg-indigo-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Financial Reports & Analytics</h2>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                <strong>Revenue Growth:</strong> +15% YoY |
                <strong>Expense Ratio:</strong> 65% |
                <strong>Profit Margin:</strong> 20%
            </p>
            <div class="relative w-full h-[260px]">
                <canvas id="analyticsChart"></canvas>
            </div>
            <div class="mt-4 space-x-2">
                <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-400">Download Report</button>
                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-400">View Analytics</button>
            </div>
        </div>

    </div>
</main>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Budget Chart
    const budgetCtx = document.getElementById('budgetChart').getContext('2d');
    new Chart(budgetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Spent', 'Remaining'],
            datasets: [{
                data: [3200000, 1800000],
                backgroundColor: ['#ef4444', '#10b981'],
                borderWidth: 1,
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Analytics Chart
    const analyticsCtx = document.getElementById('analyticsChart').getContext('2d');
    new Chart(analyticsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [400000, 450000, 500000, 550000, 600000, 650000],
                borderColor: '#6366f1',
                fill: false,
                tension: 0.4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush

@endhasrole

   
</x-app-layout>

<!-- Confirmation Modal -->
<x-confirmation-modal />

<script>
    function dashboardComponent(postUrl, token) {
        return {
            showModal: false,
            loading: false,
            successMessage: '',
            errorMessage: '',
            form: { amount: '', description: '', receipt: null },
            resetForm() { 
                this.form = { amount: '', description: '', receipt: null };
                // Reset file input
                const fileInput = document.getElementById('receipt');
                if (fileInput) fileInput.value = '';
            },
            async handleSubmit() {
                this.loading = true;
                this.successMessage = '';
                this.errorMessage = '';
                
                try {
                    // Validate receipt file
                    if (!this.form.receipt) {
                        throw new Error('Please upload a receipt.');
                    }

                    // Create FormData for file upload
                    const formData = new FormData();
                    formData.append('amount', this.form.amount);
                    formData.append('description', this.form.description || '');
                    formData.append('receipt', this.form.receipt);

                    const response = await fetch(postUrl, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': token, 
                            'Accept': 'application/json' 
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        // Handle validation errors
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat().join(' ');
                            throw new Error(errorMessages);
                        }
                        throw new Error(data.message || 'Submission failed.');
                    }
                    
                    this.successMessage = data.message;
                    this.resetForm();
                    setTimeout(() => location.reload(), 1500);
                } catch (err) {
                    this.errorMessage = err.message;
                } finally {
                    this.loading = false;
                }
            }
        };
    }
    document.addEventListener('DOMContentLoaded', (event) => {
            const spendingCanvas = document.getElementById('spendingChart');
            
            if (spendingCanvas) {
                // --- THIS IS THE FIX ---
                // These lines were incorrectly commented out.
                const ctx = spendingCanvas.getContext('2d');
                const spendingData = @json($spendingByCategory);
                
                if (spendingData && spendingData.length > 0) {
                    const labels = spendingData.map(item => item.category);
                    const data = spendingData.map(item => item.total_amount);
                    // --- END OF FIX ---

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Spending ($)',
                                data: data,
                                backgroundColor: 'rgba(99, 102, 241, 0.6)',
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { 
                                    beginAtZero: true,
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { 
                                    backgroundColor: '#1f2937',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff'
                                }
                            }
                        }
                    });
                } else {
                    const parent = spendingCanvas.parentElement;
                    spendingCanvas.remove();
                    parent.innerHTML += '<p class="text-sm text-center text-gray-500 dark:text-gray-400 italic">No approved spending data available to display.</p>';
                }
            }
        });

                    // Chart.js for System Overview
            const ctx = document.getElementById('systemChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr'],
                    datasets: [{
                        label: 'User Activity',
                        data: [120, 150, 180, 200],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    }]
                }
            });

</script>
