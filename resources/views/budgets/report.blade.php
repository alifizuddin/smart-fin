<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Budget Report') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white !important;
                font-size: 12px !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
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
    <div class="container mx-auto p-8 print:p-0">
        <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-2xl p-10 border border-gray-200 dark:border-gray-700 print-container">

            {{-- ===== HEADER SECTION ===== --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-8 border-b dark:border-gray-700">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-1">
                        Budget Performance Report
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Generated on: <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Prepared by: {{ config('app.name', 'Laravel') }}</p>
                </div>
                <div class="mt-6 md:mt-0 no-print flex space-x-3">
                    <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider shadow-sm transition">
                        🖨️ Print Report
                    </button>
                    <a href="{{ route('budgets.index', $filters) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-wider hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        ← Back
                    </a>
                </div>
            </div>

            {{-- ===== SUMMARY SECTION ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 my-10">
                @php
                    $cards = [
                        ['label' => 'Total Budget', 'value' => 'RM ' . number_format($summary['totalBudget'], 2), 'color' => 'indigo'],
                        ['label' => 'Total Spent', 'value' => 'RM ' . number_format($summary['totalSpent'], 2), 'color' => 'blue'],
                        ['label' => 'Remaining Funds', 'value' => 'RM ' . number_format($summary['remainingFunds'], 2), 'color' => $summary['remainingFunds'] >= 0 ? 'green' : 'red'],
                        ['label' => 'Over-Budget Depts', 'value' => $summary['overBudgetCount'], 'color' => 'red']
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/40 rounded-2xl p-6 text-center shadow-sm border border-{{ $card['color'] }}-100 dark:border-{{ $card['color'] }}-800 transition">
                        <h3 class="text-xs font-medium text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-300 uppercase tracking-wider">
                            {{ $card['label'] }}
                        </h3>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">
                            {{ $card['value'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- ===== DETAILED TABLE ===== --}}
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Department Breakdown</h3>

            <div class="overflow-x-auto border rounded-2xl dark:border-gray-700 shadow-md">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border-collapse">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0 z-10">
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
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $budget->department->name }}</td>
                                <td class="px-6 py-4 text-right">RM {{ number_format($budget->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right">RM {{ number_format($budget->used_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium {{ $remaining >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    RM {{ number_format(max($remaining, 0), 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1 overflow-hidden">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ min($percentage, 100) }}%</span>
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

            {{-- ===== FOOTER ===== --}}
            <div class="mt-10 text-center text-xs text-gray-400 dark:text-gray-500 border-t pt-4 dark:border-gray-700">
                <p>© {{ date('Y') }} {{ config('app.name', 'Laravel') }} — Budget Performance Report.</p>
                <p class="italic text-gray-400 mt-1">Confidential Internal Document</p>
            </div>

        </div>
    </div>
</body>
</html>
