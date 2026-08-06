@extends('layouts.app')

@section('title', 'Tracking Booking - ' . $booking->kode_booking)

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tracking Status Booking</h1>
                <p class="mt-2 text-sm text-gray-500">Lihat perjalanan status penjualan untuk booking
                    <strong>{{ $booking->kode_booking }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ $backRoute }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Booking
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2">
                <x-card title="Status Tracker">
                    <x-status-tracker :booking="$booking" :histories="$booking->statusHistory" :current-status="$booking->statusPenjualan?->status_saat_ini?->value" :total-terverifikasi="$totalTerverifikasi" />
                </x-card>

                <x-card title="Detail Timeline">
                    <div class="space-y-4">
                        @forelse($booking->statusHistory as $history)
                            <div class="rounded-3xl border border-gray-200 bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ \App\Enums\StatusPenjualan::tryFrom($history->status_sesudah)?->label() ?? ucwords(str_replace('_', ' ', $history->status_sesudah)) }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $history->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                                    </div>
                                    <x-badge :status="$history->status_sesudah" />
                                </div>
                                @if ($history->catatan)
                                    <p class="mt-3 text-sm text-gray-600">{{ $history->catatan }}</p>
                                @endif
                                @if ($history->user)
                                    <p class="mt-2 text-xs text-gray-500">Diubah oleh: {{ $history->user->nama_lengkap }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada riwayat status.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Ringkasan Booking">
                    <div class="space-y-4 text-sm text-gray-700">
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Kode Booking</span>
                            <span>{{ $booking->kode_booking }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Konsumen</span>
                            <span>{{ $booking->konsumen?->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Unit</span>
                            <span>{{ $booking->unit?->kode_unit ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Marketing</span>
                            <span>{{ $booking->marketing?->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Tanggal Booking</span>
                            <span>{{ $booking->tanggal_booking?->format('d M Y') ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Status Saat Ini</span>
                            <x-badge :status="$booking->statusPenjualan?->status_saat_ini?->value ?? 'booking'" />
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Status Bayar</span>
                            <x-badge :status="$booking->status_pembayaran_fee" />
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Total Bayar</span>
                            <span>Rp {{ number_format($totalTerverifikasi, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="font-semibold text-gray-900">Sisa Tagihan</span>
                            <span>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </x-card>

                <x-card title="Catatan Terakhir">
                    <p class="text-sm text-gray-600">
                        {{ $booking->statusPenjualan?->catatan ?? 'Tidak ada catatan tambahan untuk status saat ini.' }}
                    </p>
                </x-card>
            </div>
        </div>
    </div>
@endsection
