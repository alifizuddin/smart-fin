<div class="h-64 w-full relative" 
     x-data="{
        chart: null,
        init() {
            // Wait for Chart.js to load
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded'); 
                return;
            }

            const ctx = this.$refs.canvas.getContext('2d');
            
            // Initial Data from Livewire
            const labels = @js($labels);
            const data = @js($data);

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Spending ($)',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.6)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280' }
                        },
                        x: {
                            ticks: { color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280' }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        },
        updateChart(event) {
            if (this.chart) {
                this.chart.data.labels = event.detail.labels;
                this.chart.data.datasets[0].data = event.detail.data;
                this.chart.update();
            }
        }
     }"
     x-on:chart-data-updated.window="updateChart($event)"
>
    <canvas x-ref="canvas"></canvas>
    
    @if(empty($data))
        <div class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm z-10">
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No approved data available.</p>
        </div>
    @endif
</div>