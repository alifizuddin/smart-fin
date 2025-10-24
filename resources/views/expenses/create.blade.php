<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m0 0l6 6m-6-6v12" />
            </svg>
            {{ __('Submit New Expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl transition hover:shadow-xl duration-300">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    {{-- Error Alert --}}
                    @if ($errors->any())
                        <div class="mb-6 p-5 bg-red-50 dark:bg-red-900/40 border-l-4 border-red-500 rounded-lg animate-pulse">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 19a7 7 0 110-14a7 7 0 010 14z" />
                                </svg>
                                <h3 class="font-semibold">Please fix the following issues:</h3>
                            </div>
                            <ul class="mt-3 ml-8 list-disc text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
                        @csrf

                        {{-- Budget Info --}}
                        <div class="mb-6">
                            <x-input-label :value="__('Active Budget')" />
                            <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg">
                                <p class="text-gray-700 dark:text-gray-300 text-sm">
                                    <span class="font-semibold">Budget #{{ $activeBudget->id }}</span><br>
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">
                                        Period: {{ $activeBudget->period_start }} — {{ $activeBudget->period_end }}
                                    </span>
                                </p>
                            </div>
                            <input type="hidden" name="budget_id" value="{{ $activeBudget->id }}">
                        </div>

                        {{-- AI Info Section --}}
                        <div class="mb-8 border-l-4 border-indigo-500 bg-indigo-50 dark:bg-indigo-900/40 p-5 rounded-md">
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-indigo-800 dark:text-indigo-200 text-lg">
                                        Automated Expense Processing
                                    </h3>
                                    <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-300 leading-relaxed">
                                        Upload your receipt — our AI will automatically extract:
                                    </p>
                                    <ul class="list-disc list-inside mt-2 text-sm text-indigo-700 dark:text-indigo-300 space-y-1">
                                        <li>Amount</li>
                                        <li>Transaction Date</li>
                                        <li>Payment Method</li>
                                        <li>Merchant Details</li>
                                        <li>Description</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-6">
                            <x-input-label for="receipt" :value="__('Upload Receipt (PDF, JPG, PNG)')" />
                            <div class="mt-2 flex flex-col sm:flex-row items-center gap-4">
                                <input id="receipt" type="file" name="receipt" required
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4
                                           file:rounded-md file:border-0 file:text-sm file:font-semibold
                                           file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200
                                           dark:file:bg-indigo-800 dark:file:text-indigo-200 dark:hover:file:bg-indigo-700
                                           cursor-pointer transition" />
                            </div>
                            <x-input-error :messages="$errors->get('receipt')" class="mt-2" />
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Upload a clear, readable image of your receipt (max: 2MB).
                            </p>

                            {{-- Preview --}}
                            <div id="previewContainer" class="hidden mt-4 border rounded-lg overflow-hidden">
                                <img id="previewImage" class="w-full object-cover" alt="Receipt Preview">
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <a href="{{ route('dashboard') }}"
                               class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md
                                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button id="submitBtn" class="ms-4 flex items-center gap-2">
                                <svg id="loadingSpinner" class="hidden w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>{{ __('Submit Expense') }}</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS for preview & loading --}}
    <script>
        const receiptInput = document.getElementById('receipt');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const form = document.getElementById('expenseForm');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('loadingSpinner');

        // Preview uploaded image
        receiptInput?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
            }
        });

        // Loading state
        form?.addEventListener('submit', () => {
            submitBtn.setAttribute('disabled', 'true');
            spinner.classList.remove('hidden');
        });
    </script>
</x-app-layout>
