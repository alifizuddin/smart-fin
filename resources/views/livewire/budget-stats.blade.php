<div>
    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Budget</p>
                    <p class="text-2xl font-bold mt-1">RM {{ number_format($summary['totalBudget'], 2) }}</p>
                </div>
                <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-blue-800 dark:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800/30 text-indigo-800 dark:text-indigo-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-indigo-700 dark:text-indigo-400">Total Spent</p>
                    <p class="text-2xl font-bold mt-1">RM {{ number_format($summary['totalSpent'], 2) }}</p>
                </div>
                <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-indigo-800 dark:text-indigo-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-400">Remaining Funds</p>
                    <p class="text-2xl font-bold mt-1 {{ $summary['remainingFunds'] < 0 ? 'text-red-600' : '' }}">RM {{ number_format($summary['remainingFunds'], 2) }}</p>
                </div>
                <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-green-800 dark:text-green-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 text-red-800 dark:text-red-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-red-700 dark:text-red-400">Over-Budget Depts</p>
                    <p class="text-2xl font-bold mt-1">{{ $summary['overBudgetCount'] }}</p>
                </div>
                <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-red-800 dark:text-red-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
            </div>
        </div>
    </div>
</div>
