<div class="w-full h-full">
    <canvas id="staffSpendingChart"></canvas>

    <script>
        document.addEventListener('livewire:navigated', () => {
            initSpendingChart();
        });

        // Also run on first load
        if (document.readyState !== 'loading') {
            initSpendingChart();
        } else {
            document.addEventListener('DOMContentLoaded', initSpendingChart);
        }

        function initSpendingChart() {
            const ctx = document.getElementById('staffSpendingChart');
            if (!ctx) return;

            // Destroy existing chart if it exists to prevent overlap
            if (window.staffSpendingChartInstance) {
                window.staffSpendingChartInstance.destroy();
            }

            const chartData = @json($chartData);

            window.staffSpendingChartInstance = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
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
