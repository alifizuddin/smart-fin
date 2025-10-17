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
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Over-Budget Departments</h3>
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
                    <h3 class="text-xl font-semibold mb-4">Budget Details</h3>
                    
                    {{-- Action & Search Bar --}}
                    <div class="flex items-center justify-between flex-wrap md:flex-nowrap gap-4 p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 rounded-t-xl">
                        {{-- Action Dropdown --}}
                        <div class="flex items-center gap-2">
                            <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction"
                                    class="inline-flex items-center text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-2 focus:ring-blue-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                                Action
                                <svg class="w-3 h-3 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 10 6" aria-hidden="true">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <div id="dropdownAction"
                                 class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-44 dark:bg-gray-700 dark:divide-gray-600">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Approve</a></li>
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reject</a></li>
                                </ul>
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-gray-600 dark:hover:text-red-400">Delete Budget</a>
                                </div>
                            </div>
                        </div>

                        {{-- Search Bar --}}
                        <div class="relative w-full md:w-80">
                            <input type="text" id="table-search-budgets"
                                   class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                   placeholder="Search for budgets...">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0">
                                <tr>
                                    <th class="p-4">
                                        <input id="checkbox-all" type="checkbox"
                                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                    </th>
                                    <th class="px-6 py-3">Department</th>
                                    <th class="px-6 py-3">Total Amount</th>
                                    <th class="px-6 py-3">Used / Remaining</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgets as $budget)
                                    @php
                                        $percentage = $budget->total_amount > 0
                                            ? round(($budget->used_amount / $budget->total_amount) * 100, 1)
                                            : 0;
                                        $remaining = $budget->total_amount - $budget->used_amount;
                                    @endphp

                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-150">
                                        <td class="p-4">
                                            <input type="checkbox" id="checkbox-{{ $budget->id }}"
                                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $budget->department->name }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-200">
                                            ${{ number_format($budget->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="w-full mb-1 text-sm text-gray-600 dark:text-gray-400">
                                                <span class="font-semibold">${{ number_format($budget->used_amount, 2) }}</span> 
                                                used — <span>${{ number_format($remaining, 2) }}</span> left
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                <div class="h-2.5 rounded-full 
                                                    @if($percentage < 70) bg-green-500 
                                                    @elseif($percentage < 90) bg-yellow-500 
                                                    @else bg-red-500 @endif"
                                                     style="width: {{ $percentage }}%">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $budget->status === 'active' 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                {{ ucfirst($budget->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('budgets.show', $budget) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">View</a>
                                            @can('is-admin')
                                                <a href="{{ route('budgets.edit', $budget) }}"
                                                   class="ml-3 text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Optional Pagination --}}
                    <div class="mt-4">
                        {{ $budgets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <script>
            
            console.log('Pie chart element:', document.getElementById('departmentPieChart'));
            console.log('Bar chart element:', document.getElementById('departmentBarChart'));   

            window.addEventListener('load', function () {
            // Data from controller
            const pieData = @json($charts['pie']);
            const barData = @json($charts['bar']);

            // 1. Pie Chart

            const pieCtx = document.getElementById('departmentPieChart');
            if (pieCtx) {
                const pieCtx = document.getElementById('departmentPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(pieData),
                    datasets: [{
                        data: Object.values(pieData),
                        backgroundColor: [
                            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', 
                            '#6366F1', '#3B82F6', '#8B5CF6', '#D946EF'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    }
                }
            });
            }       
            
            // 2. Bar Chart
            const barCtx = document.getElementById('departmentBarChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(barData),
                    datasets: [
                        {
                            label: 'Total Budget',
                            data: Object.values(barData).map(d => d.total),
                            backgroundColor: '#60A5FA',
                        },
                        {
                            label: 'Amount Used',
                            data: Object.values(barData).map(d => d.used),
                            backgroundColor: '#F87171',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false,
                        },
                        y: {
                            beginAtZero: true,
                            stacked: false
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    }
                }
            });
        });
        </script>
    @endpush
</x-app-layout>