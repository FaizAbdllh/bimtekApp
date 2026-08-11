@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-green-50 border border-green-100 text-green-800 p-4 rounded-xl text-xs font-semibold shadow-sm flex items-start gap-2.5']) }}>
        {{-- Ikon Checkmark Sukses --}}
        <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        
        {{-- Pesan Status --}}
        <span class="leading-relaxed">{{ $status }}</span>
    </div>
@endif