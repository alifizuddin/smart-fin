@props(['id' => 'expenseDetailsModal'])

<div id="{{ $id }}" 
     class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300">
    <div id="{{ $id }}Card"
         class="relative bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 w-full max-w-lg">
         
        <!-- Close button (top-right) -->
        <button onclick="closeDetailsModal('{{ $id }}')" 
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition text-2xl leading-none">
            &times;
        </button>

        <!-- Title -->
        <h3 id="{{ $id }}Title" class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">
            Expense Details
        </h3>

        <!-- Dynamic content -->
        <div id="{{ $id }}Content" class="space-y-4 text-gray-600 dark:text-gray-400">
            <!-- Injected dynamically -->
        </div>

        <!-- Footer -->
        <div class="mt-6 text-right">
            <button type="button" onclick="closeDetailsModal('{{ $id }}')" 
                class="px-5 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium">
                Close
            </button>
        </div>
    </div>
</div>
