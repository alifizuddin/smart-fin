<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Submit New Expense') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Add a new expense by filling in the details below
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm relative">
                
                {{-- Loading Overlay --}}
                <div id="loadingOverlay" class="hidden absolute inset-0 bg-white/90 dark:bg-gray-800/90 z-50 flex-col items-center justify-center rounded-2xl backdrop-blur-sm">
                    <svg class="w-12 h-12 text-indigo-600 animate-spin mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Analyzing Receipt...</p>
                    <p class="text-sm text-gray-500">Please wait while we extract the details.</p>
                </div>

                <div class="p-8">
                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/40 border-l-4 border-red-500 rounded-r-lg">
                            <ul class="list-disc text-sm text-red-700 dark:text-red-300 ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/40 border-l-4 border-green-500 rounded-r-lg text-green-700 dark:text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
                        @csrf

                        <div class="space-y-8" id="initialFormState">
                            {{-- Budget Selection --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Budget</label>
                                <select name="budget_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled selected>-- Choose a budget --</option>
                                    @foreach($allActiveBudgets as $budget)
                                        <option value="{{ $budget->id }}">{{ $budget->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            
                            <div class="bg-indigo-50 dark:bg-gray-900/50 border-l-4 border-indigo-600 rounded-r-lg p-6">
                            <div class="flex items-center gap-3">
                                {{-- Sparkles/Auto-awesome Icon --}}
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Automated Expense Processing</h2>
                            </div>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Upload your receipt — our AI will automatically extract:
                            </p>
                            <ul class="mt-4 list-disc list-inside space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li>Amount</li>
                                <li>Transaction Date</li>
                                <li>Payment Method</li>
                                <li>Merchant Details</li>
                                <li>Description</li>
                            </ul>
                        </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Receipt</label>
                                <div class="mt-2 flex justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 px-6 py-10 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer group" onclick="document.getElementById('receipt').click()">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">Click to upload receipt image</p>
                                    </div>
                                </div>
                                <input id="receipt" name="receipt" type="file" accept="image/*,.pdf" class="hidden" required onchange="handleFileUpload(this)">
                            </div>
                        </div>

                        {{-- CONFIRMATION SECTION (Hidden initially) --}}
                        <div id="confirmationSection" class="hidden mt-8 border-t pt-8 border-gray-200 dark:border-gray-700 animate-in fade-in slide-in-from-bottom-4">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirm Details</h3>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium border border-yellow-200">
                                    Read-Only Mode
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Preview Image --}}
                                <div class="border rounded-lg p-2 bg-gray-50 dark:bg-gray-900 shadow-inner">
                                    <img id="previewImage" class="w-full h-64 object-contain" />
                                </div>

                                {{-- READ-ONLY Fields --}}
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (RM)</label>
                                        {{-- 'readonly' attribute prevents editing --}}
                                        <input type="text" name="amount" id="amount" readonly 
                                               class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 font-bold">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                        <input type="text" name="transaction_date" id="transaction_date" readonly 
                                               class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                        <input type="text" name="category" id="category" readonly 
                                               class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                                        <input type="text" name="payment_method" id="payment_method" readonly 
                                               class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                        <textarea name="description" id="description" rows="3" readonly 
                                                  class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-8 flex justify-end gap-4">
                                {{-- Retry Button --}}
                                <button type="button" onclick="resetForm()" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Details Incorrect? Retry
                                </button>
                                
                                {{-- Confirm Button --}}
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Confirm & Submit
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Strict JavaScript Logic --}}
    <script>
        function handleFileUpload(input) {
            const file = input.files[0];
            if (!file) return;

            // 1. Show Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
            }
            reader.readAsDataURL(file);

            // 2. Show Loading
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');

            // 3. Prepare AJAX
            const formData = new FormData();
            formData.append('receipt', file);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // 4. Send to Server
            fetch("{{ route('expenses.analyze') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Success: Populate Read-Only Fields
                    document.getElementById('amount').value = data.amount;
                    document.getElementById('transaction_date').value = data.date;
                    document.getElementById('category').value = data.category;
                    document.getElementById('description').value = data.description;

                    if (data.payment_method) {
                        document.getElementById('payment_method').value = data.payment_method;
                    }

                    // Show Confirmation
                    document.getElementById('initialFormState').classList.add('hidden');
                    document.getElementById('confirmationSection').classList.remove('hidden');
                } else {
                    // Strict Failure: Do NOT show confirmation
                    alert('Extraction failed. The system could not read the receipt clearly. Please try a different image.');
                    resetForm(); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during analysis. Please try again.');
                resetForm();
            })
            .finally(() => {
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
            });
        }

        function resetForm() {
            document.getElementById('expenseForm').reset();
    
            document.getElementById('payment_method').value = '';
            document.getElementById('confirmationSection').classList.add('hidden');
            document.getElementById('initialFormState').classList.remove('hidden');
    
            document.getElementById('previewImage').src = '';
        }
    </script>
</x-app-layout>