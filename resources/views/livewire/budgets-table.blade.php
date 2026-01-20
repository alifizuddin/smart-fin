<div>
    <x-confirmation-modal 
    id="deleteBudgetModal"
    title="Delete Budget"
    message="Are you sure you want to delete this budget? This action cannot be undone."
/>

<!-- Update the delete button in the table to this: -->
    <!-- Budgets Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    
                    <th scope="col" class="px-6 py-3 cursor-pointer hover:text-indigo-600" wire:click="sortBy('department_id')">
                        Department
                        @if($sort_by === 'department_id')
                            <span class="ml-1">{{ $sort_order === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    
                    <th scope="col" class="px-6 py-3 cursor-pointer hover:text-indigo-600" wire:click="sortBy('total_amount')">
                        Total Amount
                        @if($sort_by === 'total_amount')
                            <span class="ml-1">{{ $sort_order === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    
                    <th scope="col" class="px-6 py-3">Usage</th>
                    <th scope="col" class="px-6 py-3">Budget Name</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgets as $budget)
                    @php
                        $usedAmount = $budget->expenses->sum('amount');
                        $percentage = $budget->total_amount > 0 ? min(100, round(($usedAmount / $budget->total_amount) * 100, 1)) : 0;
                        $remaining = $budget->total_amount - $usedAmount;
                        $isOverBudget = $remaining < 0;
                    @endphp
                    
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $budget->department->name ?? 'N/A' }}
                        </td>
                        
                        <td class="px-6 py-4">
                            RM {{ number_format($budget->total_amount, 2) }}
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="h-2.5 rounded-full {{ $isOverBudget ? 'bg-red-500' : 'bg-green-500' }}" 
                                    style="width: {{ $percentage }}%; max-width: 100%;">
                                </div>
                            </div>
                            <div class="text-xs mt-1 {{ $isOverBudget ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $percentage }}% ({{ $isOverBudget ? 'Exceeded by' : 'Remaining' }} RM {{ number_format(abs($remaining), 2) }})
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            {{ $budget->name }}
                        </td>
                        
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'draft' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                ][$budget->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusColors }}">
                                {{ ucfirst($budget->status) }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-right">
                            <button wire:click="viewBudget({{ $budget->id }})"
                                x-data
                                x-on:click="$dispatch('open-budget-modal')" 
                                title="View" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors mr-2" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            
                            @can('update', $budget)
                                <a href="{{ route('budgets.edit', $budget->id) }}" 
                               class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors mr-2" 
                               title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @endcan
                            
                            @can('delete', $budget)
                                <button type="button" 
                                onclick="confirmDelete({{ $budget->id }}, '{{ route('budgets.destroy', $budget) }}')"
                                class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors" 
                                title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                            No budgets found matching criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $budgets->links() }}
    </div>

    <!-- Budget View Modal -->
    <div x-data="{ show: false }" 
        x-show="show" 
        x-on:open-budget-modal.window="show = true"
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="relative w-3/4 max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Budget Details</h3>
                <button type="button" x-on:click="show = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
    <div wire:loading.flex wire:target="viewBudget" class="flex-col items-center justify-center w-full py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500 mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm font-medium">Loading budget details...</p>
    </div>

    <div wire:loading.remove wire:target="viewBudget">
        @if($budgetDetails)
            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                    'draft' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                ][$budgetDetails['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
            @endphp

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $budgetDetails['name'] }}</h4>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusColors }}">
                        {{ ucfirst($budgetDetails['status']) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $budgetDetails['department'] }}</p>
                    </div>
                    <div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Used Amount</p>
                        <p class="font-medium text-gray-900 dark:text-white">${{ $budgetDetails['used_amount'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $budgetDetails['is_over_budget'] ? 'Over Budget By' : 'Remaining' }}</p>
                        <p class="font-medium {{ $budgetDetails['is_over_budget'] ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            ${{ $budgetDetails['remaining'] }}
                        </p>
                    </div>
                
                <div class="pt-2">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                        <span>Budget Usage</span>
                        <span>{{ $budgetDetails['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="h-2.5 rounded-full {{ $budgetDetails['is_over_budget'] ? 'bg-red-500' : 'bg-green-500' }}" 
                                style="width: {{ min($budgetDetails['percentage'], 100) }}%; max-width: 100%;">
                        </div>
                    </div>
                </div>
                
                @if(!empty($budgetDetails['notes']))
                    <div class="pt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $budgetDetails['notes'] }}</p>
                    </div>
                @endif
                
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Created by {{ $budgetDetails['creator'] }} on {{ $budgetDetails['created_at'] }}
                        </div>
                        @if(isset($viewingBudget) && auth()->user()->can('update', $viewingBudget))
                            <button wire:click="editBudget({{ $budgetDetails['id'] }})" x-on:click="show = false; $dispatch('open-edit-budget-modal')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Edit Budget
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                No budget details available.
            </div>
        @endif
    </div>
        </div>
    </div>
    <!-- Edit Budget Modal -->
    <div x-data="{ show: false }" 
        x-show="show" 
        x-on:open-edit-budget-modal.window="show = true"
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
    <div class="relative w-3/4 max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Budget</h3>
            <button type="button" x-on:click="show = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
    <div wire:loading.flex wire:target="editBudget" class="flex-col items-center justify-center w-full py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500 mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm font-medium">Loading budget details...</p>
    </div>

    <div wire:loading.remove wire:target="editBudget">
        @if($editingBudget)
            <form wire:submit.prevent="updateBudget">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget Name</label>
                        <input type="text" id="name" wire:model="form.name" 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('form.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                        <select id="department_id" wire:model="form.department_id" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" class="dark:bg-gray-800">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('form.department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Amount</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" id="total_amount" wire:model="form.total_amount" 
                                   class="pl-7 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('form.total_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="status" wire:model="form.status" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="active" class="dark:bg-gray-800">Active</option>
                            <option value="pending" class="dark:bg-gray-800">Pending</option>
                            <option value="closed" class="dark:bg-gray-800">Closed</option>
                            <option value="draft" class="dark:bg-gray-800">Draft</option>
                        </select>
                        @error('form.status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea id="notes" wire:model="form.notes" rows="3" 
                                 class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                         @error('form.notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            x-on:click="show = false" 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            wire:target="updateBudget"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span wire:loading.remove wire:target="updateBudget">Update Budget</span>
                        <span wire:loading wire:target="updateBudget">Saving...</span>
                    </button>
                </div>
            </form>
        @else
             <div class="text-center py-8 text-gray-500">
                Unable to load form.
            </div>
        @endif
    </div>
    </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    function confirmDelete(budgetId, deleteUrl) {
        const modal = document.getElementById('deleteBudgetModal');
        const form = document.getElementById('deleteBudgetModalForm');
        
        // Set up the form action
        form.action = deleteUrl;
        
        // Show the modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('#deleteBudgetModalCard').classList.remove('scale-95', 'opacity-0');
        }, 10);

        // Handle form submission
        form.onsubmit = function(e) {
            e.preventDefault();
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
            });
        };
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const card = document.getElementById(modalId + 'Card');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    document.addEventListener('livewire:initialized', () => {
        // Handle modal close with the X button or clicking outside
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                @this.closeBudgetModal();
            }
        });
        
        // Close modal when clicking outside the modal content
        document.addEventListener('click', (e) => {
            const modal = document.querySelector('[x-show="show"]');
            const modalContent = document.querySelector('.relative.w-3\/4');
            
            if (modal && modalContent && !modalContent.contains(e.target) && e.target === modal) {
                @this.closeBudgetModal();
            }
        });
    });
</script>
@endpush
