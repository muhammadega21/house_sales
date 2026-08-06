@props(['unit'])

@if ($unit)
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xs">
        <div class="flex gap-3">
            @if ($unit->foto_unit)
                <img src="{{ asset('storage/' . $unit->foto_unit) }}" alt="{{ $unit->kode_unit }}"
                    class="h-20 w-20 rounded-lg object-cover border border-gray-200">
            @else
                <div class="flex h-20 w-20 items-center justify-center rounded-lg bg-gray-100 border border-gray-200">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <h4 class="text-sm font-semibold text-gray-900">{{ $unit->kode_unit }}</h4>
                <p class="text-xs text-gray-500">{{ $unit->tipe_rumah }}</p>
                <div class="mt-1">
                    <x-badge :status="$unit->kategori" />
                    <x-badge :status="$unit->status_unit" />
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $unit->perumahan?->nama_perumahan ?? '-' }}</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">Rp
                    {{ number_format($unit->harga_jual, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-500">
            <div>Luas Tanah: {{ $unit->luas_tanah }} m²</div>
            <div>Luas Bangunan: {{ $unit->luas_bangunan }} m²</div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.unit-rumah.show', $unit->id) }}"
                class="text-xs font-medium text-primary hover:text-primary-dark">
                Lihat Detail Unit →
            </a>
        </div>
    </div>
@else
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xs">
        <p class="text-sm text-gray-400">Data unit tidak tersedia</p>
    </div>
@endif
