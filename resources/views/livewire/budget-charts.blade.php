<div class="grid grid-cols-1 gap-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                @hasrole('Admin')
                    Budget Distribution
                @else
                    Spending By Category
                @endhasrole
            </h3>
            <div class="relative h-64">
                @hasrole('Admin')
                    <canvas id="departmentPieChart" wire:ignore></canvas>
                @else
                    <canvas id="categoryDoughnutChart" wire:ignore></canvas>
                @endhasrole
            </div>
        </div>
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Spending vs Budget</h3>
            <div class="relative" style="height: 350px">
                <canvas id="departmentBarChart" wire:ignore></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            let pieChart = null;
            let barChart = null;
            let doughnutChart = null;

            function initCharts() {
                // Destroy existing charts if they exist
                if (pieChart) pieChart.destroy();
                if (barChart) barChart.destroy();
                if (doughnutChart) doughnutChart.destroy();

                // Department Pie Chart
                // In the initCharts() function, update the pie chart section
@if(auth()->user()->hasRole('Admin'))
const pieCtx = document.getElementById('departmentPieChart')?.getContext('2d');
if (pieCtx && @js($this->pieChartData) && Object.keys(@js($this->pieChartData)).length > 0) {
    const pieData = {
        labels: Object.keys(@js($this->pieChartData) || {}),
        datasets: [{
            data: Object.values(@js($this->pieChartData) || {}),
            backgroundColor: [
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#F43F5E'
            ],
            borderWidth: 1
        }]
    };
    
    pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: pieData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
@endif

                // Category Doughnut Chart
                @if(!auth()->user()->hasRole('Admin'))
                const doughnutCtx = document.getElementById('categoryDoughnutChart')?.getContext('2d');
                if (doughnutCtx && Object.keys(@js($this->categoryData) || {}).length > 0) {
                    doughnutChart = new Chart(doughnutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(@js($this->categoryData) || {}),
                            datasets: [{
                                data: Object.values(@js($this->categoryData) || {}),
                                backgroundColor: [
                                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                                    '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#F43F5E'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
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
                            }
                        }
                    });
                }
                @endif

                // Department Bar Chart
                const barCtx = document.getElementById('departmentBarChart')?.getContext('2d');
                if (barCtx && @js($this->departmentData) && @js($this->departmentData).budgets && Object.keys(@js($this->departmentData).budgets).length > 0) {
                    const departmentData = @js($this->departmentData);
                    const labels = @js($this->departmentLabels);
                    const budgets = departmentData.budgets;
                    const used = departmentData.used;

                    barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Budget',
                                    data: Object.values(budgets),
                                    backgroundColor: '#3B82F6',
                                    borderColor: '#2563EB',
                                    borderWidth: 1,
                                    barPercentage: 0.4,
                                    categoryPercentage: 0.8
                                },
                                {
                                    label: 'Spent',
                                    data: Object.values(used),
                                    backgroundColor: '#10B981',
                                    borderColor: '#059669',
                                    borderWidth: 1,
                                    barPercentage: 0.4,
                                    categoryPercentage: 0.8
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return '$' + context.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                }
            }

            if (@js($this->departmentData)?.budgets && @js($this->departmentLabels)?.length > 0) {
                initCharts();
            }   

            // Update charts when Livewire updates
            Livewire.on('charts-data-loaded', () => {
                // Small delay to ensure DOM is updated
                setTimeout(initCharts, 100);
            });
        });
    </script>
@endpush