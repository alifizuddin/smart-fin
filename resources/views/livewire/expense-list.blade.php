<div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Expense Submissions</h2>
        <a href="{{ route('expenses.my-expenses') }}" class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
            View All
        </a>
    </div>

    @if($expenses->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($expenses as $expense)
                        <tr wire:key="expense-{{ $expense->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
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
                                <button 
                                    wire:click="delete({{ $expense->id }})"
                                    wire:confirm="Are you sure you want to delete this expense?"
                                    class="text-gray-400 hover:text-red-500 transition cursor-pointer"
                                    title="Delete Expense"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-9 0h12" /></svg>
                                </button>
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