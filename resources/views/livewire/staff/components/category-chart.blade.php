<div class="w-full h-full relative">
    <canvas id="staffCategoryChart"></canvas>
    
    <div id="noDataMessage" class="hidden absolute inset-0 flex items-center justify-center text-gray-400">
        No approved expenses yet
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            initCategoryChart();
        });

        if (document.readyState !== 'loading') {
            initCategoryChart();
        } else {
            document.addEventListener('DOMContentLoaded', initCategoryChart);
        }

        function initCategoryChart() {
            const ctx = document.getElementById('staffCategoryChart');
            if (!ctx) return;

             // Destroy existing chart if it exists
            if (window.staffCategoryChartInstance) {
                window.staffCategoryChartInstance.destroy();
            }

            const chartData = @json($chartData);

            if (chartData.labels.length === 0) {
                document.getElementById('noDataMessage').classList.remove('hidden');
                return;
            }

            window.staffCategoryChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151'
                            }
                        },
                         tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</div>
