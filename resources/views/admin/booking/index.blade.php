@extends('layouts.app')

@section('title', 'Booking - Admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Semua Booking</h1>
                <p class="text-sm text-gray-500">Kelola semua booking perusahaan.</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $stats['total_aktif'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Booking Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $stats['bulan_ini'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3 text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking Fee</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp
                            {{ number_format($stats['total_booking_fee'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-yellow-50 p-3 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Filter Toolbar -->
        <x-card>
            <form method="GET" action="{{ route('admin.booking.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Kode booking, nama konsumen, atau unit..."
                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                </div>
                <div class="min-w-[160px]">
                    <label for="id_marketing" class="block text-sm font-medium text-gray-700 mb-1">Marketing</label>
                    <select name="id_marketing" id="id_marketing" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Marketing</option>
                        @foreach ($marketingOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $filterMarketing == $id ? 'selected' : '' }}>
                                {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="status_pembayaran_fee" class="block text-sm font-medium text-gray-700 mb-1">Status
                        Bayar</label>
                    <select name="status_pembayaran_fee" id="status_pembayaran_fee" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        <option value="belum_bayar" {{ $filterStatusPembayaran === 'belum_bayar' ? 'selected' : '' }}>Belum
                            Bayar</option>
                        <option value="sudah_bayar" {{ $filterStatusPembayaran === 'sudah_bayar' ? 'selected' : '' }}>Sudah
                            Bayar</option>
                        <option value="refund" {{ $filterStatusPembayaran === 'refund' ? 'selected' : '' }}>Refund</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="status_penjualan" class="block text-sm font-medium text-gray-700 mb-1">Status
                        Penjualan</label>
                    <select name="status_penjualan" id="status_penjualan" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        <option value="booking" {{ $filterStatusPenjualan === 'booking' ? 'selected' : '' }}>Booking
                        </option>
                        <option value="pengajuan_kpr" {{ $filterStatusPenjualan === 'pengajuan_kpr' ? 'selected' : '' }}>
                            Pengajuan KPR</option>
                        <option value="batal" {{ $filterStatusPenjualan === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori" id="kategori" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        <option value="subsidi" {{ $filterKategori === 'subsidi' ? 'selected' : '' }}>Subsidi</option>
                        <option value="non_subsidi" {{ $filterKategori === 'non_subsidi' ? 'selected' : '' }}>Non-Subsidi
                        </option>
                    </select>
                </div>
                @if ($hasFilters)
                    <div class="flex items-end">
                        <a href="{{ route('admin.booking.index') }}"
                            class="text-sm text-red-600 hover:text-red-800 font-medium">Hapus Filter</a>
                    </div>
                @endif
            </form>
        </x-card>

        <!-- Data Table -->
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Kode Booking</th>
                            <th class="px-4 py-3">Marketing</th>
                            <th class="px-4 py-3">Konsumen</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Booking Fee</th>
                            <th class="px-4 py-3">Status Bayar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $bookings->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4 font-mono text-sm font-semibold text-gray-900">
                                    {{ $booking->kode_booking }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $booking->marketing?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $booking->konsumen?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $booking->unit?->kode_unit ?? '-' }}
                                    <span class="text-xs text-gray-400">{{ $booking->unit?->tipe_rumah ?? '' }}</span>
                                </td>
                                <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 text-gray-700">Rp
                                    {{ number_format($booking->booking_fee, 0, ',', '.') }}</td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $booking->status_pembayaran_fee->color() }}-100 text-{{ $booking->status_pembayaran_fee->color() }}-800">
                                        {{ $booking->status_pembayaran_fee->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $status = $booking->statusHistory->sortByDesc('id')->first()?->status_sesudah ?? 'booking';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ \App\Enums\StatusPenjualan::tryFrom($status)?->color() ?? 'gray' }}-100 text-{{ \App\Enums\StatusPenjualan::tryFrom($status)?->color() ?? 'gray' }}-800">
                                        {{ \App\Enums\StatusPenjualan::tryFrom($status)?->label() ?? $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.booking.show', $booking->id) }}"
                                            class="text-info hover:text-indigo-800" title="Detail">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if($status === 'booking')
                                            <a href="{{ route('admin.booking.edit', $booking->id) }}"
                                                class="text-primary hover:text-primary-dark" title="Edit">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.booking.cancel', $booking->id) }}"
                                                class="text-danger hover:text-red-800" title="Batalkan">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <x-empty-state title="Belum ada booking"
                                        message="Tidak ada data booking yang cocok dengan filter." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <x-pagination :paginator="$bookings" />
            </div>
        </x-card>
    </div>
@endsection
