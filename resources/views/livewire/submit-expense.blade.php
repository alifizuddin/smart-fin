<div>
    <button wire:click="$set('showModal', true)" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-medium shadow-lg transition transform hover:scale-105 flex items-center justify-center relative z-10">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Submit Expense
    </button>

    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Expense Submission</h2>

            @if (session()->has('message'))
                <div class="mt-3 text-sm text-green-600 bg-green-50 p-3 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="save" class="mt-4 space-y-4">
                <div>
                    <x-input-label for="amount" value="Amount ($)" />
                    <x-text-input wire:model="amount" id="amount" type="number" step="0.01" class="w-full mt-1" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Receipt</label>
                    <input type="file" wire:model="receipt" class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                    <x-input-error :messages="$errors->get('receipt')" class="mt-2" />
                    
                    <div wire:loading wire:target="receipt" class="text-xs text-blue-600 mt-1">Uploading...</div>
                </div>

                <div>
                    <x-input-label for="description" value="Description (Optional)" />
                    <textarea wire:model="description" rows="3" class="w-full mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end items-center">
                    <x-secondary-button type="button" wire:click="$set('showModal', false)">Cancel</x-secondary-button>
                    
                    <x-primary-button type="submit" class="ms-3" wire:loading.attr="disabled">
                        <span wire:loading.remove>Submit Expense</span>
                        <span wire:loading>Processing...</span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>