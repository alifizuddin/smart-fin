<div class="flex items-center">
    @if($budgets->count() > 1)
        <select 
            wire:model.live="selectedBudgetId" 
            class="text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer"
        >
            @foreach($budgets as $budget)
                <option value="{{ $budget->id }}">
                    {{ $budget->name }} ({{ \Carbon\Carbon::parse($budget->period_end)->format('M Y') }})
                </option>
            @endforeach
        </select>
    @else
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $budgets->first()?->name ?? 'No Active Budget' }}
        </span>
    @endif
</div>