<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @hasrole('Staff')
    <main
        x-data="dashboardComponent('{{ route('expenses.quickStore') }}', '{{ csrf_token() }}')"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            @php
                // Calculation Logic for Stats
                $avgDailySpending = $userExpenses->count() > 0 && $activeBudget ? 
                    round($userExpenses->where('status', 'approved')->sum('amount') / max(1, now()->diffInDays(\Carbon\Carbon::parse($activeBudget->period_start))), 2) : 0;
                
                $totalSpent = $userExpenses->where('status', 'approved')->sum('amount');
                $totalBudget = $activeBudget ? $activeBudget->total_amount : 0;
                $percent = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 2) : 0;
            @endphp

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Expenses</p>
                        <p class="text-3xl font-bold mt-1">{{ $userExpenses->count() }}</p>
                        <div class="flex items-center text-xs mt-2 text-blue-700 dark:text-blue-400">
                            <span class="font-bold mr-1">Submitted</span>
                        </div>
                    </div>
                    <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-blue-800 dark:text-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Daily Spend</p>
                        <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">${{ number_format($avgDailySpending, 2) }}</p>
                        <div class="flex items-center text-xs mt-2 text-gray-500">
                            <span>Per active day</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget Used</p>
                        <p class="text-3xl font-bold mt-1 {{ $percent > 90 ? 'text-red-600' : ($percent > 75 ? 'text-yellow-600' : 'text-gray-900 dark:text-gray-100') }}">{{ $percent }}%</p>
                        <div class="flex items-center text-xs mt-2 {{ $percent > 90 ? 'text-red-600' : 'text-green-600' }}">
                            <span class="mr-1">{{ $percent > 90 ? '!' : '✓' }}</span>
                            <span>{{ $percent > 90 ? 'Critical Level' : 'Healthy Level' }}</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>

            @php
                $remaining = max($totalBudget - $totalSpent, 0);
            @endphp
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Remaining</p>
                        <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">${{ number_format($remaining, 2) }}</p>
                        <div class="flex items-center text-xs mt-2 text-gray-500">
                            <span>Available funds</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
            
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Budget Overview</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Track your spending against active budgets</p>
                    </div>
                    @if($userBudgets->count() > 1)
                        <form action="{{ route('dashboard') }}" method="GET" id="budgetSelectorForm">
                            <select name="budget_id" onchange="document.getElementById('budgetSelectorForm').submit()" class="text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($userBudgets as $budget)
                                    <option value="{{ $budget->id }}" {{ $activeBudget && $activeBudget->id == $budget->id ? 'selected' : '' }}>{{ $budget->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>

                @if($activeBudget)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Total Limit</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalBudget, 2) }}</span>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Spent</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalSpent, 2) }}</span>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Remaining</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($remaining, 2) }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Progress</span>
                            <span class="font-bold {{ $percent > 90 ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full {{ $percent > 90 ? 'bg-red-600' : ($percent > 75 ? 'bg-yellow-500' : 'bg-green-600') }}" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-right mt-1">Valid until {{ \Carbon\Carbon::parse($activeBudget->period_end)->format('M d, Y') }}</p>
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500 dark:text-gray-400 italic">No active budget found.</div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">AI Suggestions</h2>
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold px-2 py-1 rounded">
                        Tips
                    </span>
                </div>
                
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                    @forelse($aiSuggestions ?? [] as $suggestion)
                        <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg transition-colors">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg mt-0.5">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100">Optimization Tip</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $suggestion }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No suggestions available.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Expense Submissions</h2>
                    <button class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        View All
                    </button>
                </div>
                
                @if($userExpenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($userExpenses->take(5) as $expense)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="py-3 pl-2">
                                            <div class="flex items-center space-x-3">
                                                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg text-indigo-600 dark:text-indigo-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ $expense->category }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-right pr-4">
                                            <p class="font-bold text-sm text-gray-900 dark:text-gray-100">${{ number_format($expense->amount, 2) }}</p>
                                            @php
                                                $statusColors = [
                                                    'pending'  => 'text-yellow-600 dark:text-yellow-400',
                                                    'approved' => 'text-green-600 dark:text-green-400',
                                                    'rejected' => 'text-red-600 dark:text-red-400',
                                                ];
                                            @endphp
                                            <p class="text-xs font-medium {{ $statusColors[$expense->status] ?? 'text-gray-500' }}">
                                                {{ ucfirst($expense->status) }}
                                            </p>
                                        </td>
                                        <td class="py-3 text-right w-10">
                                            <form action="{{ route('expenses.delete', $expense->id) }}" method="POST" onsubmit="event.preventDefault(); confirmAction('{{ route('expenses.delete', $expense->id) }}', 'delete', 'confirmationModal', 'expense');" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-9 0h12" /></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500 dark:text-gray-400 text-sm italic">
                        No expense submissions yet.
                    </div>
                @endif
            </div>

            <div class="grid grid-rows-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center justify-center relative">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 absolute top-6 left-6">Status</h2>
                    <div class="relative w-32 h-32 mt-4">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3"></path>
                            <path class="{{ $percent > 90 ? 'text-red-500' : 'text-green-500' }}" stroke-dasharray="{{ $percent }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $percent }}%</p>
                            <p class="text-[10px] text-gray-400">Used</p>
                        </div>
                    </div>
                    <div class="text-xs mt-6 text-center text-gray-500 dark:text-gray-400">
                        Spending is {{ $percent > 80 ? 'high' : 'normal' }}
                    </div>
                </div>

                <div class="bg-gray-900 dark:bg-black p-6 rounded-xl shadow-lg flex flex-col justify-between text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <h2 class="text-lg font-semibold relative z-10">Quick Action</h2>
                    <p class="text-sm text-gray-400 relative z-10 mt-1">Submit new expense</p>
                    
                    <button @click="showModal = true" class="mt-4 w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-medium shadow-lg transition transform hover:scale-105 flex items-center justify-center relative z-10">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Submit Expense
                    </button>
                </div>
            </div>
        </div>

        <template x-if="showModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Expense Submission</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in your expense details below.</p>

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
                            <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Receipt</label>
                            <input type="file" id="receipt" name="receipt" accept="image/*,.pdf" @change="form.receipt = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 dark:file:bg-indigo-500 dark:hover:file:bg-indigo-600" required>
                        </div>
                        <div>
                            <x-input-label for="description" value="Description (Optional)" />
                            <textarea id="description" x-model="form.description" rows="3" class="w-full mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="e.g. Team lunch at restaurant"></textarea>
                        </div>
                        <div class="mt-6 flex justify-end items-center">
                            <x-secondary-button type="button" @click="showModal = false">Cancel</x-secondary-button>
                            <x-primary-button type="submit" class="ms-3" x-bind:disabled="loading">
                                <span x-show="!loading">Submit Expense</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-green-700 dark:text-green-400">Pending Approvals</p>
                        <p class="text-3xl font-bold mt-1">{{ $pendingExpenses->count() }}</p>
                        <div class="flex items-center text-xs mt-2 text-green-700 dark:text-green-400">
                            <span class="font-bold mr-1">Action Required</span>
                        </div>
                    </div>
                    <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-green-800 dark:text-green-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Amount</p>
                        <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">${{ number_format($pendingExpenses->sum('amount'), 2) }}</p>
                        <div class="flex items-center text-xs mt-2 text-blue-600 dark:text-blue-400">
                            <span class="mr-1">↑</span>
                            <span>Awaiting review</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Anomalies Detected</p>
                        <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">{{ count($anomalyAlerts) }}</p>
                        <div class="flex items-center text-xs mt-2 {{ count($anomalyAlerts) > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            <span class="mr-1">{{ count($anomalyAlerts) > 0 ? '!' : '✓' }}</span>
                            <span>{{ count($anomalyAlerts) > 0 ? 'Check alerts' : 'System healthy' }}</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Forecast Trend</p>
                        <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">--</p>
                        <div class="flex items-center text-xs mt-2 text-gray-500">
                            <span>Analysis pending</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
            
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Financial Analytics</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Spending by Category</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-full">Monthly View</span>
                    </div>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="spendingChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">AI Alerts</h2>
                    <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-bold px-2 py-1 rounded">
                        {{ count($anomalyAlerts) }} New
                    </span>
                </div>
                
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                    <ul class="space-y-2">
                        @forelse($anomalyAlerts as $alert)
                        <li class="flex flex-col text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 p-3 rounded-md border border-red-100 dark:border-red-800/40 shadow-sm">
                            <span class="font-medium">{{ $alert['message'] }}</span>
                            <span class="text-xs text-red-500 dark:text-red-400 mt-1 self-end">{{ $alert['date'] }}</span>
                        </li>
                        @empty
                            <li class="text-sm text-gray-500 dark:text-gray-400 italic">No current anomalies detected.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Action Required: Approvals</h2>
                    <button class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                        + Review All
                    </button>
                </div>
                
                @if($pendingExpenses->count() > 0)
                    <div class="space-y-4">
                        @foreach ($pendingExpenses as $expense)
                            <div class="flex justify-between items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg transition border border-transparent hover:border-gray-100 dark:hover:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        {{ substr($expense->user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ $expense->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->budget->name }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right hidden sm:block">
                                        <p class="font-bold text-sm text-gray-900 dark:text-gray-100">${{ number_format($expense->amount, 2) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->category }}</p>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        Pending
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500 dark:text-gray-400 text-sm italic">
                        No pending expenses for review.
                    </div>
                @endif
            </div>

            <div class="grid grid-rows-2 gap-6">
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center justify-center relative">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 absolute top-6 left-6">Forecasting</h2>
                    
                    <div class="relative w-32 h-32 mt-4">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3"></path>
                            <path class="text-indigo-500" stroke-dasharray="60, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center">
                            <p class="text-xl font-bold text-gray-900 dark:text-gray-100">Wait</p>
                            <p class="text-[10px] text-gray-400">Data N/A</p>
                        </div>
                    </div>
                    
                    <div class="w-full mt-6 space-y-2">
                         <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></span><span class="text-gray-600 dark:text-gray-300">Projected</span></div>
                            <span class="font-medium text-gray-900 dark:text-gray-100">--</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-600 mr-2"></span><span class="text-gray-600 dark:text-gray-300">Remaining</span></div>
                            <span class="font-medium text-gray-900 dark:text-gray-100">--</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 dark:bg-black p-6 rounded-xl shadow-lg flex flex-col justify-between text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/20 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    
                    <h2 class="text-lg font-semibold relative z-10">Quick Actions</h2>
                    
                    <div class="flex items-center justify-around mt-4 relative z-10">
                        <button class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition group" title="Download Reports">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </button>
                        
                        <button class="bg-indigo-600 hover:bg-indigo-500 p-4 rounded-full shadow-lg transform hover:scale-105 transition" title="New Budget">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        
                        <button class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition group" title="Settings">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @endhasrole

@hasrole('Admin')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Users</p>
                        <p class="text-3xl font-bold mt-1">1,250</p>
                        <div class="flex items-center text-xs mt-2 text-blue-700 dark:text-blue-400">
                            <span class="font-bold mr-1">+12%</span>
                            <span>this month</span>
                        </div>
                    </div>
                    <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-blue-800 dark:text-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-green-700 dark:text-green-400">System Uptime</p>
                        <p class="text-3xl font-bold mt-1">99.9%</p>
                        <div class="flex items-center text-xs mt-2 text-green-700 dark:text-green-400">
                            <span class="font-bold mr-1">✓</span>
                            <span>Operational</span>
                        </div>
                    </div>
                    <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-green-800 dark:text-green-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Modules</p>
                        <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">95%</p>
                        <div class="flex items-center text-xs mt-2 text-gray-500">
                            <span>System components</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Security Alerts</p>
                        <p class="text-3xl font-bold mt-1 text-red-600 dark:text-red-400">1</p>
                        <div class="flex items-center text-xs mt-2 text-red-600 dark:text-red-400">
                            <span class="mr-1">!</span>
                            <span>Requires attention</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
            
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">System Activity</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Real-time user load and server performance</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-full">Last 24 Hours</span>
                    </div>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="systemChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Alerts & Logs</h2>
                    <button class="text-blue-600 dark:text-blue-400 text-xs font-bold hover:underline">View All</button>
                </div>
                
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                    <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg transition-colors">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mt-0.5">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100">Security Anomaly</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Unusual login from IP 192.168.1.1</p>
                            <span class="text-[10px] text-gray-400 mt-1 block">2 mins ago</span>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg transition-colors">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mt-0.5">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100">System Update</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daily maintenance completed successfully.</p>
                            <span class="text-[10px] text-gray-400 mt-1 block">4 hours ago</span>
                        </div>
                    </div>
                </div>

                <button class="mt-4 w-full py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                    Export System Logs
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Compliance Engine</h2>
                    <button class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                        Edit Rules
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-100 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Global Spending Limit</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">$10,000</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Per category / month</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-100 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Approved Vendors</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                Vendor A
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                Vendor B
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                + Add New
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Policy Status</h3>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 100%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>Policies Active</span>
                        <span>Last updated: Today</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-rows-2 gap-6">
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center justify-center relative">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 absolute top-6 left-6">System Health</h2>
                    <div class="relative w-32 h-32 mt-4">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3"></path>
                            <path class="text-green-500" stroke-dasharray="98, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center">
                            <p class="text-xl font-bold text-gray-900 dark:text-gray-100">Good</p>
                        </div>
                    </div>
                    <div class="text-xs mt-6 text-center text-gray-500 dark:text-gray-400">
                        All systems operational
                    </div>
                </div>

                <div class="bg-gray-900 dark:bg-black p-6 rounded-xl shadow-lg flex flex-col justify-between text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/20 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <h2 class="text-lg font-semibold relative z-10">Admin Tools</h2>
                    
                    <div class="grid grid-cols-3 gap-2 mt-4 relative z-10">
                        <button class="flex flex-col items-center justify-center p-2 rounded-lg bg-white/10 hover:bg-white/20 transition group">
                            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span class="text-[10px]">Users</span>
                        </button>
                        <button class="flex flex-col items-center justify-center p-2 rounded-lg bg-white/10 hover:bg-white/20 transition group">
                            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-[10px]">Settings</span>
                        </button>
                        <button class="flex flex-col items-center justify-center p-2 rounded-lg bg-white/10 hover:bg-white/20 transition group">
                            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="text-[10px]">Reports</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
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
                const ctx = spendingCanvas.getContext('2d');
                const spendingData = @json($spendingByCategory);
                
                if (spendingData && spendingData.length > 0) {
                    const labels = spendingData.map(item => item.category);
                    const data = spendingData.map(item => item.total_amount);

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
