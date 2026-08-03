@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran</h1>
            <p class="text-sm text-gray-500">Kelola dan verifikasi semua pembayaran dari semua marketing.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Menunggu Verifikasi</p>
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
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Diverifikasi Hari Ini</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['diverifikasi_hari_ini'] ?? 0 }}</p>
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
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ditolak Hari Ini</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['ditolak_hari_ini'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-red-50 p-3 text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Terverifikasi</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">Rp
                        {{ number_format($stats['total_terverifikasi'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 8v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Filter Toolbar -->
    <x-card>
        <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="flex flex-wrap gap-4">
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
                        placeholder="Kode booking, nama konsumen, atau no referensi..."
                        class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm transition focus:border-primary focus:ring-primary">
                </div>
            </div>

            <div class="min-w-[160px]">
                <label for="status_verifikasi" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status_verifikasi" id="status_verifikasi" onchange="this.closest('form').submit()"
                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    @foreach (\App\Enums\StatusVerifikasi::cases() as $status)
                        <option value="{{ $status->value }}" {{ $filterStatus === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-[160px]">
                <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                <select name="jenis_pembayaran" id="jenis_pembayaran" onchange="this.closest('form').submit()"
                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    @foreach (\App\Enums\JenisPembayaran::cases() as $jenis)
                        <option value="{{ $jenis->value }}" {{ $filterJenis === $jenis->value ? 'selected' : '' }}>{{ $jenis->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-[200px]">
                <label for="id_marketing" class="block text-sm font-medium text-gray-700 mb-1">Marketing</label>
                <select name="id_marketing" id="id_marketing" onchange="this.closest('form').submit()"
                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Marketing</option>
                    @foreach ($marketingOptions as $id => $nama)
                        <option value="{{ $id }}" {{ $filterMarketing == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-[130px]">
                <label for="tanggal_from" class="block text-sm font-medium text-gray-700 mb-1">Tgl. Dari</label>
                <input type="date" name="tanggal_from" id="tanggal_from" value="{{ $filterTanggalFrom }}"
                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
            </div>

            <div class="min-w-[130px]">
                <label for="tanggal_to" class="block text-sm font-medium text-gray-700 mb-1">Tgl. Sampai</label>
                <input type="date" name="tanggal_to" id="tanggal_to" value="{{ $filterTanggalTo }}"
                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    Filter
                </button>
            </div>

            @if ($search !== '' || $filterStatus !== '' || $filterJenis !== '' || $filterMarketing !== '' || $filterTanggalFrom !== '' || $filterTanggalTo !== '')
                <div class="flex items-end">
                    <a href="{{ route('admin.pembayaran.index') }}"
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
                        <th class="px-4 py-3 w-12">
                            <input type="checkbox" id="checkboxSelectAll" class="rounded border-gray-300 text-primary">
                        </th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kode Booking</th>
                        <th class="px-4 py-3">Konsumen</th>
                        <th class="px-4 py-3">Marketing</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3 text-right">Nominal (Rp)</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Bukti</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($pembayarans as $pembayaran)
                        <tr class="{{ $pembayaran->status_verifikasi === \App\Enums\StatusVerifikasi::Pending ? 'bg-yellow-50' : '' }} transition hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $pembayaran->id }}" class="checkbox-pembayaran rounded border-gray-300 text-primary">
                            </td>
                            <td class="px-4 py-4 text-gray-700 whitespace-nowrap">
                                {{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</td>
                            <td class="px-4 py-4 font-mono text-sm font-semibold text-gray-900">
                                {{ $pembayaran->booking?->kode_booking ?? '-' }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $pembayaran->booking?->konsumen?->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $pembayaran->booking?->marketing?->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border bg-{{ $pembayaran->jenis_pembayaran->color() }}-100 text-{{ $pembayaran->jenis_pembayaran->color() }}-800">
                                    {{ $pembayaran->jenis_pembayaran->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $pembayaran->metode_bayar ? \App\Enums\MetodeBayar::tryFrom($pembayaran->metode_bayar)?->label() : '-' }}</td>
                            <td class="px-4 py-4">
                                @if ($pembayaran->bukti_bayar)
                                    <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                                        class="inline-block h-10 w-10 rounded-lg border border-gray-200 object-cover bg-gray-100 bg-center bg-no-repeat bg-cover"
                                        style="background-image: url({{ asset('storage/' . $pembayaran->bukti_bayar) }});">
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border bg-{{ $pembayaran->status_verifikasi->color() }}-100 text-{{ $pembayaran->status_verifikasi->color() }}-800">
                                    {{ $pembayaran->status_verifikasi->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if ($pembayaran->status_verifikasi === \App\Enums\StatusVerifikasi::Pending)
                                        <a href="{{ route('admin.pembayaran.verifikasi', $pembayaran->id) }}"
                                            class="font-semibold text-primary transition hover:text-primary-dark" title="Verifikasi">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.pembayaran.tolak', $pembayaran->id) }}"
                                            class="font-semibold text-danger hover:text-red-800" title="Tolak">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}"
                                        class="font-semibold text-gray-600 transition hover:text-gray-900" title="Detail">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-7 9.542-9.542 9.542S3.732 15.057 2.458 12z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <x-empty-state title="Belum ada pembayaran"
                                    message="Belum ada pembayaran yang perlu diverifikasi." />
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxSelectAll = document.getElementById('checkboxSelectAll');
    const checkboxes = document.querySelectorAll('.checkbox-pembayaran');

    checkboxSelectAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked) {
                checkboxSelectAll.checked = false;
            }
        });
    });
});
</script>
@endpush
