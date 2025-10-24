<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @hasrole('Staff')
    <main
        x-data="dashboardComponent('{{ route('expenses.quickStore') }}', '{{ csrf_token() }}')"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8"
    >

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
                <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">Submit a new expense in seconds.</p>

                <button
                    type="button"
                    @click="showModal = true"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-md font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700 transition"
                >
                    Submit Expense
                </button>
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
                                <th class="px-4 py-3 text-left font-semibold">Date Submitted</th>
                                <th class="px-4 py-3 text-left font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($userExpenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $expense->category }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$expense->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $expense->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <form action="{{ route('expenses.delete', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        You have no expense submissions yet.
                                    </td>
                                </tr>
                            @endforelse
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
                    @forelse($aiSuggestions ?? [] as $suggestion)
                        <li class="text-sm text-gray-700 dark:text-gray-300 bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-md shadow-sm border border-yellow-100 dark:border-yellow-800/40">
                            {{ $suggestion }}
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 dark:text-gray-400 italic">No suggestions available.</li>
                    @endforelse
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

                @php
                    $total = $budgetOverview['total_budget'] ?? 0;
                    $spent = $budgetOverview['spent'] ?? 0;
                    $remaining = max($total - $spent, 0);
                    $percent = $total > 0 ? round(($spent / $total) * 100, 2) : 0;
                @endphp

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Budget:</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Spent:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">${{ number_format($spent, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Remaining:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($remaining, 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                    <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-700" style="width: {{ $percent }}%"></div>
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
                    @forelse($approvalFollowUps ?? [] as $followUp)
                        <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $followUp['title'] }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Approver: {{ $followUp['approver'] }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $followUp['status'] == 'Awaiting Approval' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                {{ $followUp['status'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No approval items pending.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Expense Modal -->
        <template x-if="showModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Expense Submission</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in your expense details below.</p>

                    <!-- Alerts -->
                    <template x-if="successMessage">
                        <div class="mt-3 text-sm text-green-600 dark:text-green-400 p-3 bg-green-50 dark:bg-green-900/30 rounded-md border border-green-200 dark:border-green-700" x-text="successMessage"></div>
                    </template>
                    <template x-if="errorMessage">
                        <div class="mt-3 text-sm text-red-600 dark:text-red-400 p-3 bg-red-50 dark:bg-red-900/30 rounded-md border border-red-200 dark:border-red-700" x-text="errorMessage"></div>
                    </template>

                    <form @submit.prevent="handleSubmit" class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="amount" value="Amount ($)" />
                            <x-text-input id="amount" type="number" x-model="form.amount" step="0.01" required class="w-full mt-1" />
                        </div>

                        <div class="mb-6">
        <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Upload Receipt
        </label>
        <input type="file" id="receipt" name="receipt" accept="image/*,.pdf"
               class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-md file:border-0
                      file:text-sm file:font-semibold
                      file:bg-indigo-600 file:text-white
                      hover:file:bg-indigo-700
                      dark:file:bg-indigo-500 dark:hover:file:bg-indigo-600"
               required>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Accepted formats: JPG, PNG, or PDF (max 5MB)
        </p>
    </div>

                        <div>
                            <x-input-label for="description" value="Description (Optional)" />
                            <textarea id="description" x-model="form.description" rows="3" class="w-full mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="e.g. Team lunch at restaurant"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end items-center">
    <x-secondary-button type="button" @click="showModal = false">
        Cancel
    </x-secondary-button>

    <x-primary-button 
        type="submit" 
        class="ms-3" 
        x-bind:disabled="loading"
    >
        <span x-show="!loading">Submit Expense</span>
        <span x-show="loading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting...
        </span>
    </x-primary-button>
</div>
                    </form>
                </div>
            </div>
        </template>
    </main>
    @endhasrole

    @hasrole('Manager')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Action Required: Approvals -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Action Required: Approvals</h2>
            </div>

            <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">
                Review and approve or reject expenses submitted by your department staff.
            </p>

            @if($pendingExpenses->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Staff</th>
                                <th class="px-4 py-3 text-left font-semibold">Amount</th>
                                <th class="px-4 py-3 text-left font-semibold">Category</th>
                                <th class="px-4 py-3 text-left font-semibold">Date</th>
                                <th class="px-4 py-3 text-left font-semibold">Receipt</th>
                                <th class="px-4 py-3 text-left font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pendingExpenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $expense->user->name }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $expense->category }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($expense->transaction_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        @if($expense->receipt_path)
                                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                                        @else
                                            <span class="text-gray-400 italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 space-x-2">
                                        <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 dark:text-green-400 hover:underline">Approve</button>
                                        </form>
                                        <form action="{{ route('expenses.reject', $expense) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
                    No pending expenses for your department.
                </div>
            @endif
        </div>

        <!-- AI Alerts -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 dark:bg-orange-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M6 20h12l-6-16z"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">AI Alerts</h2>
            </div>

            <ul class="space-y-2">
                {{--@foreach($anomalyAlerts as $alert)
                    <li class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 p-3 rounded-md border border-red-100 dark:border-red-800/40 shadow-sm">
                        {{ $alert }}
                    </li>
                @endforeach--}}
                <li class="text-sm text-gray-500 dark:text-gray-400 italic">No current anomalies detected.</li>
            </ul>
        </div>

        <!-- AI Expense Forecasting -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 dark:bg-indigo-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">AI Forecasting</h2>
            </div>

            {{--<div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="flex justify-between">
                    <span>Next Month Prediction:</span>
                    <span class="font-semibold">${{ number_format($forecasting['next_month_prediction'], 2) }}</span>
                </div>
                <p class="italic">{{ $forecasting['trend'] }}</p>
            </div>--}}
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Forecasting data not available yet.</p>
        </div>

        <!-- Spending by Category Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 md:col-span-2">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 dark:bg-purple-900/40 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V9a2 2 0 012-2h2a2 2 0 012 2v10m6 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Spending by Category</h2>
            </div>

            <canvas id="spendingChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Chart Script -->
    <script>
        {{--const ctx = document.getElementById('spendingChart').getContext('2d');
        const spendingData = @json($spendingByCategory);
        const labels = spendingData.map(item => item.category);
        const data = spendingData.map(item => item.amount);--}}

        new Chart(ctx, {
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
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1f2937' }
                }
            }
        });
    </script>
</main>
@endhasrole


    
</x-app-layout>

<script>
    function dashboardComponent(postUrl, token) {
        return {
            showModal: false,
            loading: false,
            successMessage: '',
            errorMessage: '',
            form: { amount: '', category: '', description: '' },
            resetForm() { this.form = { amount: '', category: '', description: '' }; },
            async handleSubmit() {
                this.loading = true;
                this.successMessage = '';
                this.errorMessage = '';
                try {
                    const response = await fetch(postUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body: JSON.stringify(this.form)
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Submission failed.');
                    this.successMessage = data.message;
                    this.resetForm();
                    setTimeout(() => location.reload(), 1500);
                } catch (err) {
                    this.errorMessage = err.message;
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>
