<div class="flex items-center gap-3 select-none" {{ $attributes }}>
    {{-- Arsip Gambar Lambang Kemendikbud / BBPMP --}}
    <img src="{{ asset('images/logo-bbpmp.png') }}" 
         alt="Logo BBPMP Sumbar" 
         class="h-10 w-auto object-contain drop-shadow-sm"
         onerror="this.style.display='none'; document.getElementById('fallback-text-logo').classList.remove('hidden')">
    
    {{-- Teks Identitas Nomenklatur Lembaga Resmi --}}
    <div class="text-left font-sans tracking-wide leading-tight">
        <span class="block text-xs font-black text-primary-600 uppercase tracking-wider">SI-Bimtek</span>
        <span class="block text-[11px] font-extrabold text-gray-800 uppercase tracking-wide">BBPMP Prov. Sumbar</span>
    </div>
</div>