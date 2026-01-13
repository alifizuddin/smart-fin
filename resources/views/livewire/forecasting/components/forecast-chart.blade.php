<div class="w-full h-full">
    <canvas id="forecastingChart"></canvas>

    <script>
        document.addEventListener('livewire:navigated', () => {
            initForecastChart(@json($chartData));
        });

        if (document.readyState !== 'loading') {
            initForecastChart(@json($chartData));
        } else {
            document.addEventListener('DOMContentLoaded', () => initForecastChart(@json($chartData)));
        }

        // Listen for updates from Livewire
        window.addEventListener('chart-data-updated', event => {
            initForecastChart(event.detail.data);
        });

        function initForecastChart(chartData) {
            const ctx = document.getElementById('forecastingChart');
            if (!ctx) return;

            if (window.forecastingChartInstance) {
                window.forecastingChartInstance.destroy();
            }

            window.forecastingChartInstance = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(209, 213, 219, 0.2)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'RM ' + value; // Simple formatting
                                },
                                color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                            }
                        }
                    }
                }
            });
        }
    </script>
</div>
