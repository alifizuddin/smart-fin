<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Budget Report') }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="container mx-auto p-8">
        <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-10 border border-gray-200 dark:border-gray-700 print-container">

            {{-- Header --}}
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-8 border-b dark:border-gray-700">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-1">Budget Performance Report</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Generated on: {{ \Carbon\Carbon::now()->format('M d, Y') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 no-print">
                    <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                        Print Report
                    </button>
                    <a href="{{ route('budgets.index', $filters) }}"
                        class="ml-2 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 my-10">
                <div class="bg-indigo-50 dark:bg-indigo-900/40 rounded-xl p-5 text-center shadow-sm">
                    <h3 class="text-sm font-medium text-indigo-600 dark:text-indigo-300 uppercase tracking-wide">Total Budget</h3>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">${{ number_format($summary['totalBudget'], 2) }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/40 rounded-xl p-5 text-center shadow-sm">
                    <h3 class="text-sm font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wide">Total Spent</h3>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">${{ number_format($summary['totalSpent'], 2) }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/40 rounded-xl p-5 text-center shadow-sm">
                    <h3 class="text-sm font-medium text-green-600 dark:text-green-300 uppercase tracking-wide">Remaining Funds</h3>
                    <p class="text-3xl font-bold {{ $summary['remainingFunds'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-2">
                        ${{ number_format($summary['remainingFunds'], 2) }}
                    </p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/40 rounded-xl p-5 text-center shadow-sm">
                    <h3 class="text-sm font-medium text-red-600 dark:text-red-300 uppercase tracking-wide">Over-Budget Depts</h3>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $summary['overBudgetCount'] }}</p>
                </div>
            </div>

            {{-- Table --}}
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detailed Breakdown</h3>
            <div class="overflow-x-auto border rounded-xl dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0">
                        <tr>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3 text-right">Total Budget</th>
                            <th class="px-6 py-3 text-right">Used Amount</th>
                            <th class="px-6 py-3 text-right">Remaining</th>
                            <th class="px-6 py-3 text-right w-1/5">Usage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @forelse ($budgets as $budget)
                            @php
                                $percentage = $budget->total_amount > 0 ? round(($budget->used_amount / $budget->total_amount) * 100, 1) : 0;
                                $remaining = $budget->total_amount - $budget->used_amount;
                                $barColor = $percentage < 70 ? 'bg-green-500' : ($percentage < 90 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $budget->department->name }}</td>
                                <td class="px-6 py-4 text-right">${{ number_format($budget->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right">${{ number_format($budget->used_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium {{ $remaining >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ${{ number_format($remaining, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1">
                                        <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ $percentage }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500 dark:text-gray-400">No budget data available for this report.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="mt-10 text-center text-xs text-gray-400 dark:text-gray-500 border-t pt-4 dark:border-gray-700">
                <p>© {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All Rights Reserved.</p>
            </div>

        </div>
    </div>
</body>
</html>
