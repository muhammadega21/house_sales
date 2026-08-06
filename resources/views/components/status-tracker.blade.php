@props(['booking', 'histories' => null, 'currentStatus' => null, 'totalTerverifikasi' => 0])

@php
    $steps = \App\Enums\StatusPenjualan::cases();
    $currentStatus = $currentStatus ?? ($booking->statusPenjualan?->status_saat_ini?->value ?? 'booking');
    $historyStatuses = collect($histories?->pluck('status_sesudah')->toArray() ?? []);
    $sisaTagihan = max(0, ($booking->unit?->harga_jual ?? 0) - $totalTerverifikasi);

    $currentStatusMeta = \App\Enums\StatusPenjualan::from($currentStatus);
    $currentStatusClasses = match ($currentStatusMeta->color()) {
        'gray' => [
            'tag' => 'bg-gray-50 text-gray-700',
            'dot' => 'bg-gray-600 text-white',
            'dot-soft' => 'bg-gray-100 text-gray-700',
        ],
        'amber' => [
            'tag' => 'bg-amber-50 text-amber-700',
            'dot' => 'bg-amber-600 text-white',
            'dot-soft' => 'bg-amber-100 text-amber-700',
        ],
        'indigo' => [
            'tag' => 'bg-indigo-50 text-indigo-700',
            'dot' => 'bg-indigo-600 text-white',
            'dot-soft' => 'bg-indigo-100 text-indigo-700',
        ],
        'blue' => [
            'tag' => 'bg-blue-50 text-blue-700',
            'dot' => 'bg-blue-600 text-white',
            'dot-soft' => 'bg-blue-100 text-blue-700',
        ],
        'emerald' => [
            'tag' => 'bg-emerald-50 text-emerald-700',
            'dot' => 'bg-emerald-600 text-white',
            'dot-soft' => 'bg-emerald-100 text-emerald-700',
        ],
        'red' => [
            'tag' => 'bg-red-50 text-red-700',
            'dot' => 'bg-red-600 text-white',
            'dot-soft' => 'bg-red-100 text-red-700',
        ],
        default => [
            'tag' => 'bg-gray-50 text-gray-700',
            'dot' => 'bg-gray-600 text-white',
            'dot-soft' => 'bg-gray-100 text-gray-700',
        ],
    };

    $paymentColor = $booking->status_pembayaran_fee->color();
    $paymentClasses = match ($paymentColor) {
        'gray' => ['badge' => 'bg-gray-100 text-gray-800', 'bar' => 'bg-gray-500'],
        'emerald' => ['badge' => 'bg-emerald-100 text-emerald-800', 'bar' => 'bg-emerald-500'],
        'blue' => ['badge' => 'bg-blue-100 text-blue-800', 'bar' => 'bg-blue-500'],
        'red' => ['badge' => 'bg-red-100 text-red-800', 'bar' => 'bg-red-500'],
        'indigo' => ['badge' => 'bg-indigo-100 text-indigo-800', 'bar' => 'bg-indigo-500'],
        'amber' => ['badge' => 'bg-amber-100 text-amber-800', 'bar' => 'bg-amber-500'],
        default => ['badge' => 'bg-gray-100 text-gray-800', 'bar' => 'bg-gray-500'],
    };
@endphp

<div class="space-y-6">
    <div class="overflow-x-auto">
        <div style="min-width: 700px;" class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
                @foreach ($steps as $index => $step)
                    @php
                        $isActive = $step->value === $currentStatus;
                        $isCompleted = $historyStatuses->contains($step->value) && !$isActive;
                        $stepColor = $step->color();
                        $stepClasses = match ($stepColor) {
                            'gray' => [
                                'active' => 'bg-gray-600 text-white',
                                'complete' => 'bg-gray-100 text-gray-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-gray-700 font-semibold',
                            ],
                            'amber' => [
                                'active' => 'bg-amber-600 text-white',
                                'complete' => 'bg-amber-100 text-amber-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-amber-700 font-semibold',
                            ],
                            'indigo' => [
                                'active' => 'bg-indigo-600 text-white',
                                'complete' => 'bg-indigo-100 text-indigo-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-indigo-700 font-semibold',
                            ],
                            'blue' => [
                                'active' => 'bg-blue-600 text-white',
                                'complete' => 'bg-blue-100 text-blue-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-blue-700 font-semibold',
                            ],
                            'emerald' => [
                                'active' => 'bg-emerald-600 text-white',
                                'complete' => 'bg-emerald-100 text-emerald-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-emerald-700 font-semibold',
                            ],
                            'red' => [
                                'active' => 'bg-red-600 text-white',
                                'complete' => 'bg-red-100 text-red-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-red-700 font-semibold',
                            ],
                            default => [
                                'active' => 'bg-gray-600 text-white',
                                'complete' => 'bg-gray-100 text-gray-700',
                                'label' => 'text-gray-500',
                                'label-active' => 'text-gray-700 font-semibold',
                            ],
                        };
                        $circleClasses = $isActive
                            ? $stepClasses['active']
                            : ($isCompleted
                                ? $stepClasses['complete']
                                : 'bg-gray-100 text-gray-500');
                        $labelClasses = $isActive ? $stepClasses['label-active'] : $stepClasses['label'];
                    @endphp

                    <div class="relative flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full {{ $circleClasses }}">
                            <span class="text-sm font-semibold">{{ $index + 1 }}</span>
                        </div>
                        <p class="mt-3 text-xs leading-5 {{ $labelClasses }}">
                            {{ $step->label() }}
                        </p>
                        @if ($index < count($steps) - 1)
                            <div
                                class="absolute left-full top-6 h-px w-16 bg-gray-200 sm:left-auto sm:top-auto sm:-ml-6 sm:-mt-6 sm:w-full">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tahap Saat Ini</p>
                        <p class="mt-2 text-xl font-semibold text-gray-900">
                            {{ \App\Enums\StatusPenjualan::from($currentStatus)->label() }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ \App\Enums\StatusPenjualan::from($currentStatus)->description() }}</p>
                    </div>
                    <div class="rounded-3xl {{ $currentStatusClasses['tag'] }} px-4 py-2 text-sm font-semibold">
                        {{ ucwords(str_replace('_', ' ', $currentStatus)) }}
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Diperbarui</p>
                        <p class="mt-2 text-sm text-gray-900">
                            {{ $booking->statusPenjualan?->tanggal_perubahan?->format('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Oleh</p>
                        <p class="mt-2 text-sm text-gray-900">
                            {{ $booking->statusPenjualan?->diubahOleh?->nama_lengkap ?? 'Sistem' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Progress Pembayaran</p>
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Total Bayar</span>
                            <span>Rp {{ number_format($totalTerverifikasi, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3 h-3 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $paymentClasses['bar'] }}"
                                style="width: {{ min(100, $booking->unit?->harga_jual ? round(($totalTerverifikasi / $booking->unit->harga_jual) * 100) : 0) }}%">
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ $booking->unit?->harga_jual ? round(($totalTerverifikasi / $booking->unit->harga_jual) * 100) : 0 }}%
                            dari Rp {{ number_format($booking->unit?->harga_jual ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Detail Booking</p>
                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <div class="flex justify-between gap-3">
                            <span>Kode</span>
                            <span class="font-semibold">{{ $booking->kode_booking }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span>Konsumen</span>
                            <span class="font-semibold">{{ $booking->konsumen?->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span>Unit</span>
                            <span class="font-semibold">{{ $booking->unit?->kode_unit ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span>Marketing</span>
                            <span class="font-semibold">{{ $booking->marketing?->nama_lengkap ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-900">Detail Status & Timeline</h2>
                <p class="mt-2 text-sm text-gray-500">Lihat riwayat lengkap langkah penjualan dari booking hingga status
                    terakhir.</p>

                <div class="mt-6">
                    <x-status-timeline :histories="$histories" :current-status="$currentStatus" />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Ringkasan Status</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-700">
                    <div class="flex items-center justify-between gap-3">
                        <span>Status Penjualan</span>
                        <x-badge :status="$currentStatus" />
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>Status Pembayaran</span>
                        <span
                            class="inline-flex items-center rounded-full {{ $paymentClasses['badge'] }} px-2.5 py-1 text-xs font-semibold">{{ $booking->status_pembayaran_fee->label() }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>Sisa Tagihan</span>
                        <span class="font-semibold">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>Tanggal Booking</span>
                        <span>{{ $booking->tanggal_booking->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Catatan Terakhir</h3>
                <p class="mt-3 text-sm text-gray-600">
                    {{ $booking->statusPenjualan?->catatan ?? 'Tidak ada catatan tambahan.' }}
                </p>
            </div>
        </div>
    </div>
</div>
