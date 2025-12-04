<div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">AI Suggestions</h2>
        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold px-2 py-1 rounded">
            Tips
        </span>
    </div>
    
    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
        @forelse($suggestions as $suggestion)
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
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">No suggestions available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>