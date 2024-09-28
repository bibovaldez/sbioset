@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-blue-100'
            : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700';
@endphp

<a
    {{ $attributes->merge(['class' => 'flex items-center text-sm font-medium transition-colors duration-150 ease-in-out ' . $classes]) }}>
    {{ $slot }}
</a>
