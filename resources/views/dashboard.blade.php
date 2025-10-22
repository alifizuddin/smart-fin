<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @hasrole('Staff')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

        <!-- GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Quick Action -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 dark:bg-blue-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Action</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">Easily submit a new expense with just one click.</p>
                <button class="w-full bg-blue-600 text-white py-2.5 rounded-md font-medium hover:bg-blue-700 transition">Submit Expense</button>
            </div>

            <!-- My Expense Submissions -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 dark:bg-green-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">My Expense Submissions</h2>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Category</th>
                                <th class="px-4 py-3 text-left font-semibold">Amount</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-4 py-3 text-left font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- @foreach($expenseSubmissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $submission['title'] }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${{ number_format($submission['amount'], 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $submission['status'] == 'Approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                                            {{ $submission['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $submission['date'] }}</td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AI Suggestions -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-100 dark:bg-orange-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">AI Suggestions</h2>
                </div>
                <ul class="space-y-2">
                    {{-- @foreach($aiSuggestions as $suggestion)
                        <li class="text-sm text-gray-700 dark:text-gray-300 bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-md shadow-sm border border-yellow-100 dark:border-yellow-800/40">{{ $suggestion }}</li>
                    @endforeach --}}
                </ul>
            </div>

            <!-- Budget Overview -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 dark:bg-purple-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Budget Overview</h2>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Budget:</span>
                        {{-- <span class="font-semibold text-gray-900 dark:text-gray-100">${{ number_format($budgetOverview['total_budget'], 2) }}</span> --}}
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Spent:</span>
                        {{-- <span class="font-semibold text-red-600 dark:text-red-400">${{ number_format($budgetOverview['spent'], 2) }}</span> --}}
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Remaining:</span>
                        {{-- <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($budgetOverview['remaining'], 2) }}</span> --}}
                    </div>
                </div>

                <div class="mt-4 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                    {{-- <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-700" style="width: {{ ($budgetOverview['spent'] / $budgetOverview['total_budget']) * 100 }}%"></div> --}}
                </div>
            </div>

            <!-- Approval Follow-Ups -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 dark:bg-red-900/40 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Approval Follow-Ups</h2>
                </div>
                <div class="space-y-4">
                    {{-- @foreach($approvalFollowUps as $followUp)
                        <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $followUp['title'] }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Approver: {{ $followUp['approver'] }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $followUp['status'] == 'Awaiting Approval' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                {{ $followUp['status'] }}
                            </span>
                        </div>
                    @endforeach --}}
                </div>
            </div>

        </div>
    </main>
    @endhasrole
</x-app-layout>
