@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>