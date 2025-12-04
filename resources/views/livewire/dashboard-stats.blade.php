<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Expenses</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['total_count'] }}</p>
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
                <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">${{ number_format($stats['avg_daily'], 2) }}</p>
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
                <p class="text-3xl font-bold mt-1 {{ $stats['is_critical'] ? 'text-red-600' : ($stats['percent'] > 75 ? 'text-yellow-600' : 'text-gray-900 dark:text-gray-100') }}">{{ $stats['percent'] }}%</p>
                <div class="flex items-center text-xs mt-2 {{ $stats['is_critical'] ? 'text-red-600' : 'text-green-600' }}">
                    <span class="mr-1">{{ $stats['is_critical'] ? '!' : '✓' }}</span>
                    <span>{{ $stats['is_critical'] ? 'Critical Level' : 'Healthy Level' }}</span>
                </div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-600 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 rounded-xl shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Remaining</p>
                <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-gray-100">${{ number_format($stats['remaining'], 2) }}</p>
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