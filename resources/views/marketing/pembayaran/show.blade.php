@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Pembayaran</h1>
            <p class="text-sm text-gray-500">Kode: {{ $pembayaran->booking?->kode_booking ?? '-' }}</p>
        </div>
        <a href="{{ route('marketing.pembayaran.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14l9-9 9 9" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Info Booking -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Booking</p>
            <p class="mt-1 text-lg font-semibold text-gray-900 font-mono">{{ $pembayaran->booking?->kode_booking ?? '-' }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Konsumen</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $pembayaran->booking?->konsumen?->nama_lengkap ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $pembayaran->booking?->konsumen?->nik ?? '' }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unit</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $pembayaran->booking?->unit?->kode_unit ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $pembayaran->booking?->unit?->tipe_rumah ?? '' }} - {{ $pembayaran->booking?->unit?->perumahan?->nama_perumahan ?? '' }}</p>
        </x-card>
    </div>

    <!-- Detail Pembayaran -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Pembayaran</p>
            <p class="mt-2">
                <span
                    class="inline-flex items-center rounded-md px-3 py-1 text-sm font-semibold border bg-{{ $pembayaran->jenis_pembayaran->color() }}-100 text-{{ $pembayaran->jenis_pembayaran->color() }}-800">
                    {{ $pembayaran->jenis_pembayaran->label() }}
                </span>
            </p>
        </x-card>

        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status Verifikasi</p>
            <p class="mt-2">
                <span
                    class="inline-flex items-center rounded-md px-3 py-1 text-sm font-semibold border bg-{{ $pembayaran->status_verifikasi->color() }}-100 text-{{ $pembayaran->status_verifikasi->color() }}-800">
                    {{ $pembayaran->status_verifikasi->label() }}
                </span>
            </p>
        </x-card>

        <x-card class="sm:col-span-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nominal</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</p>
        </x-card>

        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Bayar</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</p>
        </x-card>

        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Metode Bayar</p>
            <p class="mt-1 text-gray-900">{{ \App\Enums\MetodeBayar::tryFrom($pembayaran->metode_bayar)?->label() ?? '-' }}</p>
        </x-card>

        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">No. Referensi</p>
            <p class="mt-1 text-gray-900">{{ $pembayaran->no_referensi ?: '-' }}</p>
        </x-card>

        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Input</p>
            <p class="mt-1 text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</p>
        </x-card>
    </div>

    @if ($pembayaran->status_verifikasi === \App\Enums\StatusVerifikasi::Ditolak)
        <x-card title="Catatan Verifikasi">
            <p class="text-sm text-gray-700">{{ $pembayaran->catatan_verifikasi ?? 'Ditolak tanpa catatan.' }}</p>
        </x-card>
    @endif

    <!-- Bukti Bayar -->
    @if ($pembayaran->bukti_bayar)
        <x-card title="Bukti Pembayaran">
            @php
                $extension = strtolower(pathinfo($pembayaran->bukti_bayar, PATHINFO_EXTENSION));
                $isPdf = in_array($extension, ['pdf']);
            @endphp
            <div class="flex items-center gap-6">
                @if ($isPdf)
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50">
                        <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h10M7 14h10" />
                        </svg>
                    </div>
                @else
                    <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti Bayar"
                        class="h-32 w-32 rounded-xl object-cover border border-gray-200">
                @endif
                <div class="flex-1">
                    <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                       class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-7 9.542-9.542 9.542S3.732 15.057 2.458 12z" />
                        </svg>
                        Lihat Bukti
                    </a>
                    <p class="mt-2 text-xs text-gray-400">{{ basename($pembayaran->bukti_bayar) }}</p>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Timeline -->
    <x-card title="Riwayat Verifikasi">
        <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            <ul class="space-y-6">
                <li class="relative flex items-start gap-4">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 ring-2 ring-white">
                        <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 8v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900">Diinput</p>
                        <p class="text-sm text-gray-500">Pembayaran telah diinput oleh marketing,</p>
                        <p class="text-xs text-gray-400">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </li>

                <li class="relative flex items-start gap-4">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-100 ring-2 ring-white">
                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900">Menunggu Verifikasi</p>
                        <p class="text-sm text-gray-500">Pembayaran sedang dalam antrean verifikasi oleh Admin.</p>
                        <p class="text-xs text-gray-400">Status: <span
                                class="font-medium text-amber-800">{{ $pembayaran->status_verifikasi->label() }}</span></p>
                    </div>
                </li>

                @if ($pembayaran->status_verifikasi === \App\Enums\StatusVerifikasi::Diverifikasi)
                    <li class="relative flex items-start gap-4">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 ring-2 ring-white">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Diverifikasi</p>
                            <p class="text-sm text-gray-500">Pembayaran telah diverifikasi oleh Admin.</p>
                            <p class="text-xs text-gray-400">
                                @if ($pembayaran->tanggal_verifikasi)
                                    {{ $pembayaran->tanggal_verifikasi->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </li>
                @elseif ($pembayaran->status_verifikasi === \App\Enums\StatusVerifikasi::Ditolak)
                    <li class="relative flex items-start gap-4">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-100 ring-2 ring-white">
                            <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Ditolak</p>
                            <p class="text-sm text-gray-500">Pembayaran ditolak oleh Admin.</p>
                            @if ($pembayaran->tanggal_verifikasi)
                                <p class="text-xs text-gray-400">{{ $pembayaran->tanggal_verifikasi->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </x-card>
</div>
@endsection
