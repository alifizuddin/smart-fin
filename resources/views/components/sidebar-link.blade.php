@props(['href' => '#', 'active' => false])

@php
$classes = $active ?? false
            ? 'bg-indigo-50 dark:bg-gray-700 text-indigo-700 dark:text-indigo-300 border-r-4 border-indigo-500 dark:border-indigo-400'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border-r-4 border-transparent';
$classes .= ' group flex items-center px-4 py-3 text-sm font-medium rounded-r-md transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} href="{{ $href }}" wire:navigate>
    {{ $slot }}
</a>
