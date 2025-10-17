<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Budgets Dashboard') }}
            </h2>
            @can('is-admin')
                <a href="{{ route('budgets.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition ease-in-out duration-150">
                    + Create Budget
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary Cards & Charts from Tier 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total Budget</h3>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">${{ number_format($summary['totalBudget'], 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total Spent</h3>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">${{ number_format($summary['totalSpent'], 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Remaining Funds</h3>
                    <p class="text-3xl font-bold {{ $summary['remainingFunds'] >= 0 ? 'text-green-500' : 'text-red-500' }} mt-2">${{ number_format($summary['remainingFunds'], 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Over-Budget Depts</h3>
                    <p class="text-3xl font-bold text-red-500 mt-2">{{ $summary['overBudgetCount'] }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 mb-8">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Budget by Department</h3>
                    <canvas id="departmentPieChart"></canvas>
                </div>
                <div class="lg:col-span-3 bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Spending by Department</h3>
                    <canvas id="departmentBarChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('budgets.index') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All</option>
                                    <option value="active" @selected($filters['status'] ?? '' == 'active')>Active</option>
                                    <option value="closed" @selected($filters['status'] ?? '' == 'closed')>Closed</option>
                                </select>
                            </div>
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $filters['start_date'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $filters['end_date'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Filter</button>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('budgets.bulkAction') }}" method="POST" id="bulk-action-form">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-input">
                        
                        <div class="flex items-center justify-between flex-wrap md:flex-nowrap gap-4 p-4 bg-gray-50 dark:bg-gray-900 border-y border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction"
                                        class="inline-flex items-center text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                    Bulk Action
                                    <svg class="w-3 h-3 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                </button>
                                <div id="dropdownAction" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-44 dark:bg-gray-700">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                        <li><button type="submit" name="action" value="delete" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-gray-600 dark:hover:text-red-400">Delete Selected</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th class="p-4"><input id="checkbox-all" type="checkbox" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500"></th>
                                        {{-- Tier 1: Sortable Headers --}}
                                        @php
                                            $linkClasses = 'px-6 py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600';
                                            $icon = ($filters['sort_order'] ?? 'desc') == 'desc' ? '&#9660;' : '&#9650;';
                                        @endphp
                                        <th class="{{ $linkClasses }}" onclick="window.location.href='{{ route('budgets.index', array_merge($filters, ['sort_by' => 'department', 'sort_order' => ($filters['sort_by'] ?? '') == 'department' && ($filters['sort_order'] ?? '') == 'asc' ? 'desc' : 'asc'])) }}'">Department {!! ($filters['sort_by'] ?? '') == 'department' ? $icon : '' !!}</th>
                                        <th class="{{ $linkClasses }}" onclick="window.location.href='{{ route('budgets.index', array_merge($filters, ['sort_by' => 'total_amount', 'sort_order' => ($filters['sort_by'] ?? '') == 'total_amount' && ($filters['sort_order'] ?? '') == 'asc' ? 'desc' : 'asc'])) }}'">Total Amount {!! ($filters['sort_by'] ?? '') == 'total_amount' ? $icon : '' !!}</th>
                                        <th class="px-6 py-3">Used / Remaining</th>
                                        <th class="px-6 py-3">Period</th>
                                        <th class="px-6 py-3">Created By</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($budgets as $budget)
                                        @php
                                            $percentage = $budget->total_amount > 0 ? round(($budget->used_amount / $budget->total_amount) * 100, 1) : 0;
                                            $remaining = $budget->total_amount - $budget->used_amount;
                                        @endphp
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-gray-700">
                                            <td class="p-4"><input type="checkbox" name="budget_ids[]" value="{{ $budget->id }}" class="budget-checkbox w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500"></td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $budget->department->name }}</td>
                                            <td class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-200">${{ number_format($budget->total_amount, 2) }}</td>
                                            <td class="px-6 py-4">
                                                <div class="w-full text-sm text-gray-600 dark:text-gray-400"><span>${{ number_format($remaining, 2) }}</span> left</div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700"><div class="h-2.5 rounded-full @if($percentage < 70) bg-green-500 @elseif($percentage < 90) bg-yellow-500 @else bg-red-500 @endif" style="width: {{ $percentage }}%"></div></div>
                                            </td>
                                            {{-- Tier 1: New Context Columns --}}
                                            <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($budget->period_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($budget->period_end)->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $budget->creator->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $budget->status === 'active' ? 'bg-green-100 text-white-800 dark:bg-green-900' : 'bg-red-100 text-red-800 dark:bg-red-900' }}">{{ ucfirst($budget->status) }}</span></td>
                                            <td class="px-6 py-4 text-right">
                                                <button type="button" class="view-budget-btn text-indigo-600 dark:text-indigo-400 hover:underline font-medium" data-budget-id="{{ $budget->id }}">View</button>
                                                @can('is-admin')<a href="{{ route('budgets.edit', $budget) }}" class="ml-3 text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</a>@endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center py-8 text-gray-500">No budgets found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form> <div class="mt-4">{{ $budgets->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="viewBudgetModal" tabindex="-1" aria-hidden="true"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="relative w-full max-w-3xl mx-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            
            {{-- Header --}}
            <div class="flex justify-between items-center p-6 border-b dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M12 8c-1.657 0-3 .895-3 2v4c0 1.105 1.343 2 3 2s3-.895 3-2v-4c0-1.105-1.343-2-3-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M5 8v8a7 7 0 0014 0V8" />
                    </svg>
                    Budget Details
                </h3>
                <button type="button"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                        data-modal-close>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div id="modal-body-content" class="p-6 space-y-6">
                {{-- Dynamic content will be injected here --}}
                <div class="flex justify-center py-10">
                    <span class="text-gray-400 dark:text-gray-500 text-sm">Loading details...</span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end items-center gap-3 p-6 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white font-medium"
                        data-modal-close>Close</button>
            </div>
        </div>
    </div>
</div>



    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Tier 2 Charts
                const pieCtx = document.getElementById('departmentPieChart')?.getContext('2d');
                if (pieCtx) {
                    new Chart(pieCtx, { type: 'doughnut', data: { labels: Object.keys(@json($charts['pie'])), datasets: [{ data: Object.values(@json($charts['pie'])), backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#6366F1'], hoverOffset: 4 }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
                }

                const barCtx = document.getElementById('departmentBarChart')?.getContext('2d');
                if (barCtx) {
                    new Chart(barCtx, { type: 'bar', data: { labels: Object.keys(@json($charts['bar'])), datasets: [{ label: 'Total Budget', data: Object.values(@json($charts['bar'])).map(d => d.total), backgroundColor: '#60A5FA' }, { label: 'Amount Used', data: Object.values(@json($charts['bar'])).map(d => d.used), backgroundColor: '#F87171' }] }, options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { position: 'top' } } } });
                }

                // Tier 1: Bulk Action JavaScript
                const selectAllCheckbox = document.getElementById('checkbox-all');
                const budgetCheckboxes = document.querySelectorAll('.budget-checkbox');
                const bulkActionForm = document.getElementById('bulk-action-form');
                
                selectAllCheckbox.addEventListener('change', function() {
                    budgetCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                bulkActionForm.addEventListener('submit', function(e) {
                    // Prevent submission if no action is selected from the dropdown
                    const action = e.submitter.value;
                    if (!action) {
                        e.preventDefault();
                        alert('Please select a bulk action.');
                        return;
                    }

                    // Confirm deletion
                    if (action === 'delete') {
                        if (!confirm('Are you sure you want to delete the selected budgets? This action cannot be undone.')) {
                            e.preventDefault();
                        }
                    }
                });

                // View Budget Modal
                // View Budget Modal
const viewBudgetButtons = document.querySelectorAll('.view-budget-btn');
const viewBudgetModal = document.getElementById('viewBudgetModal');
const modalBodyContent = document.getElementById('modal-body-content');
const closeModalButtons = viewBudgetModal.querySelectorAll('[data-modal-close]');

// Function to open modal
function openModal() {
    viewBudgetModal.classList.remove('hidden');
    viewBudgetModal.setAttribute('aria-hidden', 'false');
}

// Function to close modal
function closeModal() {
    viewBudgetModal.classList.add('hidden');
    viewBudgetModal.setAttribute('aria-hidden', 'true');
    modalBodyContent.innerHTML = '';
}

// Event listeners for close
closeModalButtons.forEach(btn => btn.addEventListener('click', closeModal));
viewBudgetModal.addEventListener('click', (e) => {
    if (e.target === viewBudgetModal) closeModal(); // close on overlay click
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal(); // close on Esc
});

viewBudgetButtons.forEach(button => {
    button.addEventListener('click', function () {
        const budgetId = this.dataset.budgetId;

        modalBodyContent.innerHTML = `
            <div class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-indigo-600 border-t-transparent"></div>
            </div>
        `;

        fetch(`/budgets/${budgetId}/json`)
            .then(response => response.json())
            .then(data => {
                const remaining = data.total_amount - data.used_amount;
                const percentage = data.total_amount > 0
                    ? Math.round((data.used_amount / data.total_amount) * 100)
                    : 0;

                const colorClass =
                    percentage < 70 ? 'bg-green-500' :
                    percentage < 90 ? 'bg-yellow-500' : 'bg-red-500';

                modalBodyContent.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-gray-500 text-sm uppercase dark:text-gray-400">Department</h4>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">${data.department.name}</p>
                            </div>
                            <div>
                                <h4 class="text-gray-500 text-sm uppercase dark:text-gray-400">Total Amount</h4>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">$${Number(data.total_amount).toLocaleString()}</p>
                            </div>
                            <div>
                                <h4 class="text-gray-500 text-sm uppercase dark:text-gray-400">Used Amount</h4>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">$${Number(data.used_amount).toLocaleString()}</p>
                            </div>
                            <div>
                                <h4 class="text-gray-500 text-sm uppercase dark:text-gray-400">Remaining</h4>
                                <p class="text-lg font-semibold ${remaining >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    $${remaining.toLocaleString()}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col justify-center">
                            <div class="mb-2 flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>Usage</span>
                                <span>${percentage}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="h-3 rounded-full ${colorClass}" style="width: ${percentage}%"></div>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-gray-500 text-sm uppercase dark:text-gray-400">Period</h4>
                                <p class="text-sm text-gray-800 dark:text-gray-200">${new Date(data.period_start).toLocaleDateString()} - ${new Date(data.period_end).toLocaleDateString()}</p>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-gray-500 text-sm uppercase dark:text-gray-400">Status</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.status === 'active'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">
                                    ${data.status.toUpperCase()}
                                </span>
                            </div>
                        </div>
                    </div>
                `;

                openModal();
            })
            .catch(() => {
                modalBodyContent.innerHTML = `<p class="text-center text-red-500">Failed to load budget details.</p>`;
                openModal();
            });
    });
});

            });
        </script>
    @endpush
</x-app-layout>