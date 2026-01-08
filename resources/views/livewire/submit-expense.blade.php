<div>
    {{-- Trigger Button --}}
    <button wire:click="$set('showModal', true)" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-medium shadow-lg transition transform hover:scale-105 flex items-center justify-center relative z-10">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Submit Expense
    </button>

    {{-- Main Upload Modal (Initial Step) --}}
    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-40">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">New Expense Submission</h2>

            @if (session()->has('message'))
                <div class="mb-4 text-sm text-green-600 bg-green-50 p-3 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Upload Area --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload Receipt to Scan
                </label>
                <div class="relative group">
                    <input type="file" wire:model="receipt" accept="image/*" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300 transition-all cursor-pointer border border-gray-300 dark:border-gray-600 rounded-lg">
                </div>
                <x-input-error :messages="$errors->get('receipt')" class="mt-2" />

                @if($isProcessing)
                <div class="mt-4 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center gap-3 animate-pulse">
                    <svg class="w-5 h-5 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                        Analyzing receipt with N8N...
                    </span>
                </div>
                @endif
            </div>

            <div class="flex justify-end">
                <x-secondary-button wire:click="$set('showModal', false)">
                    Cancel
                </x-secondary-button>
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmation Modal (Step 2: Pops up after AI Processing) --}}
    <x-modal name="confirm-expense" :show="$confirmingExpenseSubmission" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Confirm Extracted Details
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Please review the information extracted from your receipt before submitting.
            </p>

            <div class="mt-6 space-y-4">
                {{-- Amount & Date Row --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="amount" value="Amount ($)" />
                        <x-text-input wire:model="amount" type="number" step="0.01" class="w-full mt-1 font-bold" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="transaction_date" value="Date" />
                        <x-text-input wire:model="transaction_date" type="date" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                    </div>
                </div>

                {{-- Category --}}
                <div>
                    <x-input-label for="category" value="Category" />
                    <x-text-input wire:model="category" type="text" class="w-full mt-1" placeholder="e.g. Meals, Travel" />
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>

                {{-- Description --}}
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea wire:model="description" rows="3" class="w-full mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button wire:click="$set('confirmingExpenseSubmission', false)">
                    Cancel
                </x-secondary-button>

                <x-primary-button wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove>Confirm Submission</span>
                    <span wire:loading>Saving...</span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>