<div class="flow-root">
    @php
        $sorted = $histories ? $histories->sortBy('created_at') : collect();
        $latest = $currentStatus;
        if ($latest instanceof \BackedEnum) {
            $latest = $latest->value;
        }
        if (!$latest && $sorted->isNotEmpty()) {
            $latest = $sorted->last()->status_sesudah;
        }
        $latest = $latest ?? null;
    @endphp

    @forelse($sorted as $history)
        @php
            $isCurrent = $history->status_sesudah === $latest;
            $dotClass = $isCurrent ? 'bg-blue-500' : 'bg-emerald-500';
            $statusEnum = \App\Enums\StatusPenjualan::tryFrom($history->status_sesudah);
            $label = $statusEnum?->label() ?? $history->status_sesudah ?? '-';
            $color = $isCurrent ? 'blue' : ($statusEnum?->color() ?? 'gray');
            $bgColor = $statusEnum?->color() ?? 'gray';
        @endphp
        <div class="relative pb-8">
            @if(!$loop->last)
                <div class="absolute left-3 top-3.5 -ml-px h-full w-0.5 bg-gray-200"></div>
            @endif
            <div class="relative flex items-start">
                <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $dotClass }}"></div>
                <div class="ml-4 min-w-0 flex-1">
                    <div class="flex items-baseline justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $bgColor }}-100 text-{{ $bgColor }}-800">
                            {{ $isCurrent ? 'Aktif' : 'Selesai' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">
                        {{ $history->created_at?->format('d M Y, H:i') ?? '-' }}
                        @if($history->user?->nama_lengkap)
                            · oleh: {{ $history->user->nama_lengkap }}
                        @endif
                    </p>
                    @if($history->catatan)
                        <p class="mt-1 text-xs text-gray-600">{{ $history->catatan }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400">Tidak ada riwayat status</p>
        @endforelse

    @if($latest && $latest !== \App\Enums\StatusPenjualan::Batal->value && $latest !== \App\Enums\StatusPenjualan::SerahTerima->value)
        <div class="relative">
            <div class="relative flex items-start">
                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-300">
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                    <p class="text-sm text-gray-400">(status selanjutnya)</p>
                </div>
            </div>
        </div>
    @endif
</div>
