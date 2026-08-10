@extends('layouts.app')

@section('title', 'Data Pembayaran')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Pembayaran</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola pembayaran untuk booking properti milik Anda.</p>
            </div>
            <a href="{{ route('marketing.pembayaran.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Pembayaran
            </a>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pending</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Terverifikasi (Bulan Ini)</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">Rp
                            {{ number_format($stats['total_terverifikasi'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Ditolak</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['ditolak'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-3 text-red-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Filter Toolbar --}}
        <x-card>
            <form method="GET" action="{{ route('marketing.pembayaran.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[220px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $search }}"
                            placeholder="Kode booking atau nama konsumen..."
                            class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm transition focus:border-primary focus:ring-primary">
                    </div>
                </div>

                <div class="min-w-[160px]">
                    <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                        Pembayaran</label>
                    <select name="jenis_pembayaran" id="jenis_pembayaran" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\JenisPembayaran::cases() as $jenis)
                            <option value="{{ $jenis->value }}" {{ $filterJenis === $jenis->value ? 'selected' : '' }}>
                                {{ $jenis->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[160px]">
                    <label for="status_verifikasi" class="block text-sm font-medium text-gray-700 mb-1">Status
                        Verifikasi</label>
                    <select name="status_verifikasi" id="status_verifikasi" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\StatusVerifikasi::cases() as $status)
                            <option value="{{ $status->value }}" {{ $filterStatus === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[180px]">
                    <label for="id_booking" class="block text-sm font-medium text-gray-700 mb-1">Booking</label>
                    <select name="id_booking" id="id_booking" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Booking</option>
                        @foreach ($bookingOptions as $id => $label)
                            <option value="{{ $id }}" {{ $filterBooking == $id ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('marketing.pembayaran.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                        <svg class="mr-1 -ml-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </x-card>

        {{-- Data Table --}}
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kode Booking</th>
                            <th class="px-4 py-3">Konsumen</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3 text-right">Nominal (Rp)</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3">Status Verifikasi</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($pembayarans as $pembayaran)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $pembayarans->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4 text-gray-700 whitespace-nowrap">
                                    {{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 font-mono text-sm font-semibold text-gray-900">
                                    {{ $pembayaran->booking?->kode_booking ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $pembayaran->booking?->konsumen?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4">
                                    <x-badge :status="$pembayaran->jenis_pembayaran" />
                                </td>
                                <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $pembayaran->metode_bayar ? \App\Enums\MetodeBayar::tryFrom($pembayaran->metode_bayar)?->label() : '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge :status="$pembayaran->status_verifikasi" />
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('marketing.pembayaran.show', $pembayaran->id) }}"
                                            class="font-semibold text-primary transition hover:text-primary-dark"
                                            title="Detail">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if ($pembayaran->bukti_bayar)
                                            <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                                                class="font-semibold text-gray-600 transition hover:text-gray-900"
                                                title="Lihat Bukti">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-7 9.542-9.542 9.542S3.732 15.057 2.458 12z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <x-empty-state title="Belum ada pembayaran"
                                        message="Mulai input pembayaran untuk booking Anda." :create-route="route('marketing.pembayaran.create')"
                                        create-label="Input Pembayaran" :reset-route="route('marketing.pembayaran.index')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$pembayarans" />
            </div>
        </x-card>
    </div>
@endsection
