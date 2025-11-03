<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 1.343 3 3m4 0a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                {{ __('Create New Budget') }}
            </h2>
            <a href="{{ route('budgets.index') }}" 
               class="hidden sm:inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                
                {{-- Header Card --}}
                <div class="p-8 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 via-white to-transparent dark:from-indigo-950/40 dark:via-gray-800">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Budget Setup</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Define the budget name, department, amount, and active period for this budget entry.
                    </p>
                </div>

                {{-- Form Section --}}
                <div class="p-8">
                    <form method="POST" action="{{ route('budgets.store') }}" class="space-y-6">
                        @csrf

                        {{-- 🔹 Budget Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Budget Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" 
                                type="text" name="name" :value="old('name')" 
                                placeholder="e.g., Q4 Department Budget, Marketing 2025, etc." required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- Department --}}
                        <div>
                            <x-input-label for="department_id" :value="__('Department')" />
                            <select id="department_id" name="department_id" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 rounded-lg shadow-sm transition">
                                <option value="">Select a Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                        </div>
                        
                        {{-- Total Amount --}}
                        <div>
                            <x-input-label for="total_amount" :value="__('Total Amount ($)')" />
                            <x-text-input id="total_amount" class="block mt-1 w-full" 
                                type="number" name="total_amount" :value="old('total_amount')" required step="0.01" min="0"
                                placeholder="Enter total allocated amount" />
                            <x-input-error :messages="$errors->get('total_amount')" class="mt-2" />
                        </div>

                        {{-- Period Start & End --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="period_start" :value="__('Period Start Date')" />
                                <x-text-input id="period_start" class="block mt-1 w-full" 
                                    type="date" name="period_start" :value="old('period_start')" required />
                                <x-input-error :messages="$errors->get('period_start')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="period_end" :value="__('Period End Date')" />
                                <x-text-input id="period_end" class="block mt-1 w-full" 
                                    type="date" name="period_end" :value="old('period_end')" required />
                                <x-input-error :messages="$errors->get('period_end')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('budgets.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                Cancel
                            </a>

                            <x-primary-button class="inline-flex items-center px-6 py-3 text-sm font-semibold tracking-wider rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Create Budget') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
