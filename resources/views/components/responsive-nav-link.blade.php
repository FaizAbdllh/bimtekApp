@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pr-4 py-2.5 border-l-4 border-primary-600 rounded-r-xl text-start text-base font-bold tracking-wide text-primary-700 bg-primary-50/60 focus:outline-none focus:text-primary-800 focus:bg-primary-100 focus:border-primary-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pr-4 py-2.5 border-l-4 border-transparent rounded-r-xl text-start text-base font-semibold tracking-wide text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>