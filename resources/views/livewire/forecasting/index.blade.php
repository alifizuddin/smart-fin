<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Financial Forecasting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Control Panel -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">AI-Powered Predictions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Last Updated: {{ $lastUpdated }}
                    </p>
                </div>
                
                <button wire:click="generateForecast" wire:loading.attr="disabled" class="mt-4 md:mt-0 flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove>Generate New Forecast</span>
                    <span wire:loading>Processing with N8N...</span>
                </button>
            </div>

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Spending Trend & Forecast</h4>
                    <div class="h-80">
                         <livewire:forecasting.components.forecast-chart />
                    </div>
                </div>

                <!-- Insights Panel -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Analysis Insights</h4>
                    
                    @if($latestForecast)
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Predicted Next Month</p>
                                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                    RM {{ number_format($latestForecast->amount, 2) }}
                                </p>
                            </div>

                             <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Confidence Score</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mr-2">
                                        <div class="bg-{{ $latestForecast->confidence_score > 0.8 ? 'green' : 'yellow' }}-500 h-2.5 rounded-full" style="width: {{ $latestForecast->confidence_score * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $latestForecast->confidence_score * 100 }}%</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Why this prediction?</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                                    "{{ $latestForecast->insights }}"
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-48 text-gray-500">
                            <p>No forecast data available.</p>
                            <p class="text-sm">Click "Generate New Forecast" to start.</p>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>
