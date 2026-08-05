@extends('layouts.app')

@section('title', 'Status Penjualan')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Status Penjualan</h1>
                <p class="mt-2 text-sm text-gray-600">Daftar status penjualan lengkap dengan filter dan timeline.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            @foreach (['prospek', 'booking', 'pengajuan_kpr', 'akad', 'serah_terima', 'batal'] as $status)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500">
                        {{ \App\Enums\StatusPenjualan::from($status)->label() }}</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $summary[$status] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <x-card title="Filter">
            <form method="GET" action="{{ route('admin.status-penjualan.index') }}" class="grid gap-4 lg:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cari</label>
                    <input type="text" name="search" value="{{ $search }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Kode booking atau nama konsumen" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\StatusPenjualan::cases() as $statusEnum)
                            <option value="{{ $statusEnum->value }}"
                                {{ $filterStatus === $statusEnum->value ? 'selected' : '' }}>{{ $statusEnum->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Perumahan</label>
                    <select name="perumahan"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Models\Perumahan::where('status', 'aktif')->orderBy('nama_perumahan')->get() as $perumahan)
                            <option value="{{ $perumahan->id }}" {{ $filterPerumahan == $perumahan->id ? 'selected' : '' }}>
                                {{ $perumahan->nama_perumahan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Marketing</label>
                    <select name="marketing"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Models\User::marketing()->aktif()->orderBy('nama_lengkap')->get() as $marketing)
                            <option value="{{ $marketing->id }}"
                                {{ $filterMarketing == $marketing->id ? 'selected' : '' }}>{{ $marketing->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Periode Mulai</label>
                    <input type="date" name="periode_mulai" value="{{ $periodeMulai }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Periode Selesai</label>
                    <input type="date" name="periode_selesai" value="{{ $periodeSelesai }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary" />
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">Terapkan
                        Filter</button>
                </div>
            </form>
        </x-card>

        <x-card title="Daftar Status Penjualan">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Kode Booking</th>
                            <th class="px-4 py-3">Konsumen</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Marketing</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Terakhir Diubah</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($statusPenjualans as $index => $statusPenjualan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $statusPenjualans->firstItem() + $index }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $statusPenjualan->booking?->kode_booking ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $statusPenjualan->booking?->konsumen?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $statusPenjualan->unit?->kode_unit ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $statusPenjualan->booking?->marketing?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <x-badge :status="$statusPenjualan->status_saat_ini" />
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $statusPenjualan->tanggal_perubahan?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.status-penjualan.show', $statusPenjualan->id) }}"
                                        class="text-primary hover:text-primary-dark">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data status
                                    penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $statusPenjualans->links() }}</div>
        </x-card>
    </div>
@endsection
