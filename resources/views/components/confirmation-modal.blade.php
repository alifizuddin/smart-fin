@props([
    'id' => 'confirmationModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to perform this action?',
])

<div id="{{ $id }}" 
     class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300">
    <div id="{{ $id }}Card"
         class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 w-full max-w-md relative">
         
        <div id="{{ $id }}Icon" class="w-16 h-16 flex items-center justify-center mx-auto rounded-full mb-4">
            <!-- Dynamic icon goes here -->
        </div>
        
        <h3 id="{{ $id }}Title" class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-3">
            {{ $title }}
        </h3>
        <p id="{{ $id }}Message" class="text-center text-gray-600 dark:text-gray-400 mb-6">
            {{ $message }}
        </p>

        <form id="{{ $id }}Form" method="POST" class="flex justify-center gap-4">
            @csrf
            @method('PATCH')
            <button type="button" onclick="closeModal('{{ $id }}')" 
                class="px-5 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium">
                Cancel
            </button>
            <button type="submit" id="{{ $id }}ConfirmButton" 
                class="px-5 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium">
                Confirm
            </button>
        </form>
    </div>
</div>
