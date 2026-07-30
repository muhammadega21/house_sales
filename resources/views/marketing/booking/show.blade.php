@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Booking</h1>
            <p class="text-sm text-gray-500">Kode: {{ $booking->kode_booking }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.booking.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Konsumen</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->konsumen?->nama_lengkap ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $booking->konsumen?->nik ?? '' }}</p>
            <p class="text-sm text-gray-500">{{ $booking->konsumen?->no_hp ?? '' }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unit</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->unit?->kode_unit ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $booking->unit?->tipe_rumah ?? '' }}</p>
            <p class="text-sm text-gray-500">{{ $booking->unit?->perumahan?->nama_perumahan ?? '' }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Marketing</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->marketing?->nama_lengkap ?? '-' }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Booking</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->tanggal_booking->format('d/m/Y') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Booking Fee</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($booking->booking_fee, 0, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status Bayar</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $booking->status_pembayaran_fee->color() }}-100 text-{{ $booking->status_pembayaran_fee->color() }}-800">
                    {{ $booking->status_pembayaran_fee->label() }}
                </span>
            </p>
        </x-card>
    </div>

    @if($booking->metode_bayar_fee)
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Metode Bayar</p>
            <p class="mt-1 text-gray-900">{{ \App\Enums\MetodeBayar::tryFrom($booking->metode_bayar_fee)?->label() ?? $booking->metode_bayar_fee }}</p>
        </x-card>
    @endif

    @if($booking->bukti_bayar_fee)
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Bukti Bayar</p>
            <div class="mt-2">
                <img src="{{ asset('storage/' . $booking->bukti_bayar_fee) }}" alt="Bukti Bayar" class="h-32 w-32 object-cover rounded-lg border border-gray-200">
            </div>
        </x-card>
    @endif

    @if($booking->catatan)
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</p>
            <p class="mt-1 text-gray-700">{{ $booking->catatan }}</p>
        </x-card>
    @endif

    <!-- Status History -->
    <x-card title="Riwayat Status">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Status Sebelum</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Status Sesudah</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Catatan</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Diubah Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($booking->statusHistory->sortByDesc('created_at') as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ \App\Enums\StatusPenjualan::tryFrom($history->status_sebelum)?->label() ?? $history->status_sebelum ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-700">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ \App\Enums\StatusPenjualan::tryFrom($history->status_sesudah)?->color() ?? 'gray' }}-100 text-{{ \App\Enums\StatusPenjualan::tryFrom($history->status_sesudah)?->color() ?? 'gray' }}-800">
                                    {{ \App\Enums\StatusPenjualan::tryFrom($history->status_sesudah)?->label() ?? $history->status_sesudah }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-gray-700">{{ $history->catatan ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $history->user?->nama_lengkap ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada riwayat status</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection