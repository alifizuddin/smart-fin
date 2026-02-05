<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Financial Analytics</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Department-wise spending trends</p>
        </div>
        <div class="flex space-x-2">
            <button 
                wire:click="updateTimeframe('monthly')" 
                class="px-3 py-1 text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition">
                Monthly
            </button>
            <button 
                wire:click="updateTimeframe('quarterly')" 
                class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                Quarterly
            </button>
            <button 
                wire:click="updateTimeframe('yearly')" 
                class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                Yearly
            </button>
        </div>
    </div>
    <div class="h-64 w-full relative">
        <canvas 
            x-data="{
                chart: null,
                init() {
                    const ctx = this.$el.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js($labels),
                            datasets: @js($datasets)
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1f2937',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': RM ' + context.parsed.y.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                                        callback: function(value) {
                                            return 'RM ' + value.toLocaleString();
                                        }
                                    },
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                                    },
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                    }
                                }
                            }
                        }
                    });

                    // Update chart when data changes
                    this.$watch('$wire.labels', () => {
                        this.chart.data.labels = @js($labels);
                        this.chart.data.datasets = @js($datasets);
                        this.chart.update();
                    });
                }
            }"
            x-init="init()"
            wire:ignore
        ></canvas>
    </div>
</div>