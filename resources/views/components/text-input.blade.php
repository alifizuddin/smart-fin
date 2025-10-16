@props(['disabled' => false])

{{-- Updated input styling to be consistent with welcome.blade.php --}}
<input @disabled($disabled) {!! $attributes->merge(['class' => 'mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500']) !!}>