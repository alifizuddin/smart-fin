<div>
    @if($budget)
        @php
            $totalSpent = $stats['total_spent'];
            $totalBudget = $stats['total_budget'];
            $remaining = $stats['remaining'];
            $percent = $stats['percent'];
        @endphp
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
            <p class="text-xs text-gray-500 dark:text-gray-400 text-right mt-1">Valid until {{ \Carbon\Carbon::parse($budget->period_end)->format('M d, Y') }}</p>
        </div>
    @else
        <div class="text-center py-10 text-gray-500 dark:text-gray-400 italic">No active budget found.</div>
    @endif
</div>
