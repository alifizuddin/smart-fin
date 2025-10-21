<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @hasrole('Staff')
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">


    <!-- Quick Action: Submit Expense -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <h2 class="ml-3 text-lg font-semibold text-gray-900">Quick Action</h2>
        </div>
            <p class="text-gray-600 mb-4">Submit a new expense quickly.</p>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 inline-block">Submit Expense</button>
    </div>

    <!-- My Expense Submissions -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow md:col-span-2">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h2 class="ml-3 text-lg font-semibold text-gray-900">My Expense Submissions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Category</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Amount</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($expenseSubmissions as $submission)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $submission['title'] }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">${{ number_format($submission['amount'], 2) }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $submission['status'] == 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $submission['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $submission['date'] }}</td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div> 
    </div>

    <!-- AI Suggestion/Alert -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h2 class="ml-3 text-lg font-semibold text-gray-900">AI Suggestions</h2>
        </div>
            <ul class="space-y-2">
                {{--@foreach($aiSuggestions as $suggestion)
                    <li class="text-sm text-gray-600 bg-yellow-50 p-2 rounded-md">{{ $suggestion }}</li>
                @endforeach --}}
            </ul>
    </div>

    <!-- Personal Budget Overview -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <h2 class="ml-3 text-lg font-semibold text-gray-900">Budget Overview</h2>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Budget:</span>
                {{--<span class="text-sm font-medium">${{ number_format($budgetOverview['total_budget'], 2) }}</span>--}}
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Spent:</span>
                {{--<span class="text-sm font-medium text-red-600">${{ number_format($budgetOverview['spent'], 2) }}</span> --}}
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Remaining:</span>
                {{-- <span class="text-sm font-medium text-green-600">${{ number_format($budgetOverview['remaining'], 2) }}</span> --}}
            </div>
        </div>
        <div class="mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2">
                {{--<div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($budgetOverview['spent'] / $budgetOverview['total_budget']) * 100 }}%"></div> --}}
            </div>
        </div> 
    </div>

    <!-- Approval Follow-Ups -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow md:col-span-2">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h2 class="ml-3 text-lg font-semibold text-gray-900">Approval Follow-Ups</h2>
        </div>
        <div class="space-y-4">
            {{--@foreach($approvalFollowUps as $followUp)
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-md">
                <div>
                    <h3 class="text-sm font-medium text-gray-900">{{ $followUp['title'] }}</h3>
                    <p class="text-xs text-gray-600">Approver: {{ $followUp['approver'] }}</p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs {{ $followUp['status'] == 'Awaiting Approval' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $followUp['status'] }}
                </span>
            </div>
            @endforeach --}}
        </div>
    </div>
    @endhasrole

</x-app-layout>
