<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Budgets Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage and track your department budgets and allocations
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @hasrole('Finance Admin')
                    <a href="{{ route('budgets.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white uppercase tracking-wider hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Budget
                    </a>
                @endhasrole
            </div>
        </div>
    </x-slot>

 
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @livewire('budget-stats')
            @livewire('budget-charts')
            @livewire('budgets-table')
        </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const departmentData = @json($charts['departmentData'] ?? []);
                const categoryData = @json($categoryData ?? []);
                
                // Department Pie Chart
                const deptPieChartEl = document.getElementById('departmentPieChart');
                if (deptPieChartEl && departmentData.length > 0) {
                    const ctx = deptPieChartEl.getContext('2d');
                    if (ctx) {
                        new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: departmentData.map(item => item.department),
                            datasets: [{
                                data: departmentData.map(item => item.amount),
                                backgroundColor: [
                                    '#3B82F6', // blue-500
                                    '#8B5CF6', // violet-500
                                    '#EC4899', // pink-500
                                    '#10B981', // emerald-500
                                    '#F59E0B', // amber-500
                                    '#EF4444', // red-500
                                    '#6B7280'  // gray-500
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        color: '#6B7280', // gray-500
                                        font: {
                                            size: 12
                                        },
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '70%'
                        }
                    });
                }
                
                // Category Doughnut Chart
                const categoryChartEl = document.getElementById('categoryDoughnutChart');
                if (categoryChartEl && Object.keys(categoryData).length > 0) {
                    const ctx = categoryChartEl.getContext('2d');
                    if (ctx) {
                        const categories = Object.keys(categoryData);
                        const amounts = Object.values(categoryData);
                        
                        new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: categories,
                            datasets: [{
                                data: amounts,
                                backgroundColor: [
                                    '#3B82F6', // blue-500
                                    '#8B5CF6', // violet-500
                                    '#EC4899', // pink-500
                                    '#10B981', // emerald-500
                                    '#F59E0B', // amber-500
                                    '#EF4444', // red-500
                                    '#6B7280'  // gray-500
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        color: '#6B7280', // gray-500
                                        font: {
                                            size: 12
                                        },
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '70%'
                        }
                    });
                }
                
                // Department Bar Chart
                const deptBarChartEl = document.getElementById('departmentBarChart');
                if (deptBarChartEl && departmentData.length > 0) {
                    const ctx = deptBarChartEl.getContext('2d');
                    if (ctx) {
                        const sortedData = [...departmentData].sort((a, b) => b.amount - a.amount);
                        
                        new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: sortedData.map(item => item.department),
                            datasets: [
                                {
                                    label: 'Budget',
                                    data: sortedData.map(item => item.budget),
                                    backgroundColor: '#3B82F6', // blue-500
                                    borderRadius: 4
                                },
                                {
                                    label: 'Spent',
                                    data: sortedData.map(item => item.amount),
                                    backgroundColor: '#10B981', // emerald-500
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#6B7280' // gray-500
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#E5E7EB', // gray-200
                                        borderDash: [3, 3]
                                    },
                                    ticks: {
                                        color: '#6B7280', // gray-500
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        color: '#6B7280', // gray-500
                                        boxWidth: 12,
                                        padding: 16,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.dataset.label || '';
                                            const value = context.raw || 0;
                                            return `${label}: $${value.toLocaleString()}`;
                                        }
                                    }
                                }
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            barPercentage: 0.7,
                            categoryPercentage: 0.8
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>