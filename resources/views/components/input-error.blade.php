@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs font-semibold text-red-600 space-y-1 mt-1.5 list-none pl-0']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-1">
                {{-- Ikon Peringatan Mikro Galat --}}
                <svg class="w-3.5 h-3.5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="leading-relaxed">{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif