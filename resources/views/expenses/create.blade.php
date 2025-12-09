<x-app-layout>
    {{-- Header matches the styling of the main dashboard but keeps the new clean title --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit New Expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                
                <div class="p-8">
                    {{-- Error Alert --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/40 border-l-4 border-red-500 rounded-r-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 19a7 7 0 110-14a7 7 0 010 14z" />
                                </svg>
                                <h3 class="font-semibold text-red-800 dark:text-red-200">Please fix the following issues:</h3>
                            </div>
                            <ul class="mt-2 ml-8 list-disc text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm" class="space-y-8">
                        @csrf

                        {{-- Budget Selection --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="budget_id">
                                Select Budget
                            </label>
                            <select id="budget_id" name="budget_id" required 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-indigo-600 focus:border-indigo-600 dark:focus:ring-indigo-500 dark:placeholder-gray-400 shadow-sm transition-colors">
                                
                                <option value="" disabled {{ old('budget_id') ? '' : 'selected' }}>-- Please choose a budget --</option>
                                
                                @forelse($allActiveBudgets as $budget)
                                    @php
                                        $totalSpent = $budget->expenses->sum('total_amount');
                                        $remaining = $budget->total_amount - $totalSpent;
                                    @endphp
                                    <option value="{{ $budget->id }}">
                                        {{ $budget->name }} (Remaining: ${{ number_format($remaining, 2) }})
                                    </option>
                                @empty
                                    <option value="" disabled>You have no active budgets.</option>
                                @endforelse
                            </select>
                            
                            @if($allActiveBudgets->isEmpty())
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    You cannot submit an expense because you have no active budgets.
                                </p>
                            @endif
                        </div>

                        {{-- AI Section --}}
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

                        {{-- File Upload Area --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="receipt">
                                Upload Receipt (PDF, JPG, PNG)
                            </label>
                            
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 dark:border-gray-600 px-6 py-10 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors relative">
                                <div class="text-center">
                                    {{-- Upload Icon --}}
                                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600 dark:text-gray-400 justify-center">
                                        <label for="receipt" class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 dark:focus-within:ring-offset-gray-900 hover:text-indigo-500">
                                            <span>Choose file</span>
                                            <input id="receipt" name="receipt" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="sr-only">
                                        </label>
                                        <p class="pl-1"><span> or drag and drop</span></p>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500 dark:text-gray-400 mt-1">
                                        Upload a clear, readable image of your receipt (max: 2MB).
                                    </p>
                                    <p id="fileNameDisplay" class="text-sm text-indigo-600 dark:text-indigo-400 mt-2 font-medium"></p>
                                </div>
                            </div>
                            
                            {{-- Preview Image --}}
                            <div id="previewContainer" class="hidden mt-6 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900/50">
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Receipt Preview
                                </div>
                                <img id="previewImage" class="w-full max-h-96 object-contain p-4" alt="Receipt Preview">
                            </div>
                        </div>

                        {{-- Footer / Buttons --}}
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end items-center gap-4">
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </a>
                            
                            <button type="submit" id="submitBtn" class="px-6 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg id="loadingSpinner" class="hidden w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Submit Expense</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS for preview & loading --}}
    <script>
        var receiptInput = document.getElementById('receipt');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const form = document.getElementById('expenseForm');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('loadingSpinner');

        // Preview uploaded image and show filename
        receiptInput?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) {
                fileNameDisplay.textContent = '';
                previewContainer.classList.add('hidden');
                return;
            }

            // Show filename
            fileNameDisplay.textContent = 'Selected: ' + file.name;

            // Show preview if image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                // If PDF or other format, maybe just show the name
                previewContainer.classList.add('hidden');
            }
        });

        // Loading state
        form?.addEventListener('submit', () => {
            submitBtn.setAttribute('disabled', 'true');
            spinner.classList.remove('hidden');
            submitBtn.querySelector('span').textContent = 'Processing...';
        });
    </script>
</x-app-layout>