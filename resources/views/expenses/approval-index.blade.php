<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Expense Approvals') }}
            </h2>

            <!-- Search bar -->
            <form method="GET" action="{{ route('expenses.approvals.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by user or category..."
                       class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-1.5 w-64">
                <x-primary-button>Search</x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                    Pending Expenses — <span class="text-indigo-500">{{ Auth::user()->department->name ?? 'Unknown' }}</span>
                </h3>

                <!-- Alerts -->
                @foreach (['success' => 'green', 'error' => 'red', 'info' => 'blue'] as $msg => $color)
                    @if (session($msg))
                        <div class="mb-4 p-3 rounded-md bg-{{ $color }}-100 dark:bg-{{ $color }}-900 text-{{ $color }}-800 dark:text-{{ $color }}-200">
                            {{ session($msg) }}
                        </div>
                    @endif
                @endforeach

                @if ($pendingExpenses->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-10">
                        🎉 No pending expenses for approval.
                    </p>
                @else
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase font-medium">
                                <tr>
                                    <th class="px-6 py-3 text-left">Submitted By</th>
                                    <th class="px-6 py-3 text-left">Amount</th>
                                    <th class="px-6 py-3 text-left">Category</th>
                                    <th class="px-6 py-3 text-left">Budget</th>
                                    <th class="px-6 py-3 text-left">Transaction Date</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                    <th class="px-6 py-3 text-left">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @foreach ($pendingExpenses as $expense)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $expense->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            ${{ number_format($expense->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ ucfirst($expense->category) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $expense->budget->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($expense->transaction_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm space-x-2">
                                            <!-- Approve Button -->
                                            <button type="button" onclick="confirmAction('{{ route('expenses.approve', $expense->id) }}', 'approve')" 
                                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                                Approve
                                            </button>

                                            <!-- Reject Button -->
                                            <button type="button" onclick="confirmAction('{{ route('expenses.reject', $expense->id) }}', 'reject')" 
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md transition">
                                                Reject
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            <button type="button" onclick='showDetailsModal({{ json_encode($expense) }})' class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $pendingExpenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Modals -->
    <x-confirmation-modal />
    <x-expense-details-modal />

    @push('scripts')
    <script>
        // JavaScript for both confirmation and details modals
        function confirmAction(url, action, modalId = 'confirmationModal', extraText = '') {
            const modal = document.getElementById(modalId);
            const card = document.getElementById(modalId + 'Card');
            const icon = document.getElementById(modalId + 'Icon');
            const title = document.getElementById(modalId + 'Title');
            const message = document.getElementById(modalId + 'Message');
            const confirmButton = document.getElementById(modalId + 'ConfirmButton');
            const form = document.getElementById(modalId + 'Form');

            form.action = url;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);

            if (action === 'approve') {
                icon.innerHTML = `
                    <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 p-4 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>`;
                title.textContent = 'Approve Action?';
                message.textContent = 'Confirm approval ' + (extraText ? `(${extraText})` : '') + '?';
                confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:ring-green-300 transition';
            } else {
                icon.innerHTML = `
                    <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 p-4 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                        </svg>
                    </div>`;
                title.textContent = 'Reject Action?';
                message.textContent = 'Reject this item ' + (extraText ? `(${extraText})` : '') + '?';
                confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:ring-red-300 transition';
            }
        }

        function closeModal(modalId = 'confirmationModal') {
            const modal = document.getElementById(modalId);
            const card = document.getElementById(modalId + 'Card');

            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 150);
        }

        function showDetailsModal(expense) {
            const modalId = 'expenseDetailsModal';
            const modal = document.getElementById(modalId);
            const card = document.getElementById(modalId + 'Card');
            const content = document.getElementById(modalId + 'Content');

            let receiptLink = expense.receipt_path ? `<a href="/storage/${expense.receipt_path}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">View Receipt</a>` : '<span>N/A</span>';

            content.innerHTML = `
                <p><strong>Submitted By:</strong> ${expense.user.name}</p>
                <p><strong>Amount:</strong> $${parseFloat(expense.amount).toFixed(2)}</p>
                <p><strong>Category:</strong> ${expense.category}</p>
                <p><strong>Budget:</strong> ${expense.budget ? expense.budget.name : 'N/A'}</p>
                <p><strong>Transaction Date:</strong> ${new Date(expense.transaction_date).toLocaleDateString()}</p>
                <p><strong>Description:</strong> ${expense.description || 'No description provided.'}</p>
                <p><strong>Receipt:</strong> ${receiptLink}</p>
            `;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDetailsModal(modalId = 'expenseDetailsModal') {
            const modal = document.getElementById(modalId);
            const card = document.getElementById(modalId + 'Card');

            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 150);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const confirmationModal = document.getElementById('confirmationModal');
                const detailsModal = document.getElementById('expenseDetailsModal');
                if (!confirmationModal.classList.contains('hidden')) closeModal('confirmationModal');
                if (!detailsModal.classList.contains('hidden')) closeDetailsModal('expenseDetailsModal');
            }
        });
    </script>
    @endpush

</x-app-layout>
