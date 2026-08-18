@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Penjualan</h1>
                <p class="text-sm text-gray-500">Laporan transaksi penjualan unit rumah</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}"
                    onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin h-4 w-4 mr-2\' fill=\'none\' viewBox=\'0 0 24 24\'></svg>Mengunduh...'; setTimeout(() => this.disabled=false, 3000);"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('admin.laporan.export-excel', request()->query()) }}"
                    onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin h-4 w-4 mr-2\' fill=\'none\' viewBox=\'0 0 24 24\'></svg>Mengunduh...'; setTimeout(() => this.disabled=false, 3000);"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        <x-data-table-toolbar
            :search-route="route('admin.laporan.penjualan')"
            search-placeholder="Cari nama konsumen, kode unit..."
            :per-page="$laporan['data']->perPage()"
            :total="$laporan['data']->total()"
            :filtered="$laporan['data']->total()"
            :search="request('search')"
            :has-filters="true"
            :exclude-keys="['periode_mulai', 'periode_selesai', 'id_perumahan', 'kategori', 'id_marketing', 'status']"
        >
            <x-slot:filters>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                    <div class="min-w-[160px]">
                        <label for="periode_mulai" class="mb-1 block text-sm font-medium text-gray-700">Periode Mulai</label>
                        <input type="date" name="periode_mulai" id="periode_mulai" value="{{ $filters['periode_mulai'] ?? '' }}"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    </div>
                    <div class="min-w-[160px]">
                        <label for="periode_selesai" class="mb-1 block text-sm font-medium text-gray-700">Periode Selesai</label>
                        <input type="date" name="periode_selesai" id="periode_selesai" value="{{ $filters['periode_selesai'] ?? '' }}"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    </div>
                    <div class="min-w-[160px]">
                        <label for="id_perumahan" class="mb-1 block text-sm font-medium text-gray-700">Perumahan</label>
                        <select name="id_perumahan" id="id_perumahan"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">Semua Perumahan</option>
                            @foreach($perumahanList as $perumahan)
                                <option value="{{ $perumahan->id }}" {{ ($filters['id_perumahan'] ?? '') == $perumahan->id ? 'selected' : '' }}>
                                    {{ $perumahan->nama_perumahan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <label for="kategori" class="mb-1 block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="kategori" id="kategori"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">Semua Kategori</option>
                            <option value="subsidi" {{ ($filters['kategori'] ?? '') === 'subsidi' ? 'selected' : '' }}>Subsidi</option>
                            <option value="non_subsidi" {{ ($filters['kategori'] ?? '') === 'non_subsidi' ? 'selected' : '' }}>Non-Subsidi</option>
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <label for="id_marketing" class="mb-1 block text-sm font-medium text-gray-700">Marketing</label>
                        <select name="id_marketing" id="id_marketing"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">Semua Marketing</option>
                            @foreach($marketingList as $marketing)
                                <option value="{{ $marketing->id }}" {{ ($filters['id_marketing'] ?? '') == $marketing->id ? 'selected' : '' }}>
                                    {{ $marketing->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">Semua Status</option>
                            <option value="akad" {{ ($filters['status'] ?? '') === 'akad' ? 'selected' : '' }}>Akad</option>
                            <option value="serah_terima" {{ ($filters['status'] ?? '') === 'serah_terima' ? 'selected' : '' }}>Serah Terima</option>
                        </select>
                    </div>
                </div>
            </x-slot:filters>
        </x-data-table-toolbar>

        <!-- Ringkasan -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Unit Terjual</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($laporan['ringkasan']['total_unit_terjual']) }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Nilai Penjualan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($laporan['ringkasan']['total_nilai_penjualan'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($laporan['ringkasan']['total_booking']) }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3 text-amber-600">
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Rata-rata Harga</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($laporan['ringkasan']['rata_rata_harga'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Chart: Penjualan per Bulan -->
        <x-card title="Penjualan per Bulan">
            <div class="h-80">
                <canvas id="penjualanPerBulanChart"></canvas>
            </div>
        </x-card>

        <!-- Tabel Detail Penjualan -->
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">No</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Konsumen</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unit</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kategori</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Nilai</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($laporan['data'] as $i => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-gray-500">{{ ($laporan['data']->currentPage() - 1) * $laporan['data']->perPage() + $i + 1 }}</td>
                                <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($row->tanggal_perubahan)->format('d M Y') }}</td>
                                <td class="px-3 py-3 font-medium text-gray-800">{{ $row->nama_konsumen }}</td>
                                <td class="px-3 py-3 text-gray-600">{{ $row->kode_unit }}</td>
                                <td class="px-3 py-2">
                                    <x-badge :status="$row->kategori" />
                                </td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format((float) $row->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-gray-600">{{ $row->nama_marketing }}</td>
                                <td class="px-3 py-2">
                                    <x-badge :status="$row->status_saat_ini" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data penjualan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <x-pagination :paginator="$laporan['data']" />
            </div>
        </x-card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartLabels = @json($chartLabels);
                const chartUnit = @json($chartUnit);

                const barCtx = document.getElementById('penjualanPerBulanChart');
                if (barCtx) {
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Unit Terjual',
                                data: chartUnit,
                                backgroundColor: '#2563EB',
                                borderRadius: 12,
                                maxBarThickness: 48,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#4B5563',
                                    },
                                    grid: {
                                        color: 'rgba(15, 23, 42, 0.05)',
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#374151',
                                    },
                                    grid: {
                                        display: false,
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection