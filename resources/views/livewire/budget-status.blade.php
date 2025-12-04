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
