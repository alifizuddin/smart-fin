<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Financial Forecasting') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Overview of your financial activities and key metrics
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Control Panel -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">AI-Powered Predictions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Last Updated: {{ $lastUpdated }}
                    </p>
                </div>

                <!-- Category Selector -->
                <div class="w-full md:w-64">
                    <select wire:model.live="category" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Global Overview (All)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button wire:click="generateForecast" wire:loading.attr="disabled" class="mt-4 md:mt-0 flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove>Generate New Forecast</span>
                    <span wire:loading>Processing...</span>
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
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Projected Spending</p>
                                <div class="space-y-4 mt-2">
                                    @forelse($upcomingForecasts as $forecast)
                                        <div class="border-l-2 border-indigo-400 pl-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $forecast->forecast_date->format('M Y') }}
                                            </p>
                                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                                RM {{ number_format($forecast->amount, 2) }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No forecast data available</p>
                                    @endforelse
                                </div>
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
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Analysis Insights</p>
                                @if(count($upcomingForecasts) > 0)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                        {{ $latestForecast->insights }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        Based on analysis of historical data and trends.
                                    </p>
                                @else
                                    <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                                        Generate a forecast to see detailed insights.
                                    </p>
                                @endif
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
