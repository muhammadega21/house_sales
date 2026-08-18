@extends('layouts.app')

@section('title', 'Laporan Kinerja Marketing')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Kinerja Marketing</h1>
                <p class="text-sm text-gray-500">Performa penjualan seluruh marketing</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('manajemen.laporan.export-pdf', request()->query()) }}"
                    onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin h-4 w-4 mr-2\' fill=\'none\' viewBox=\'0 0 24 24\'></svg>Mengunduh...'; setTimeout(() => this.disabled=false, 3000);"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('manajemen.laporan.export-excel', request()->query()) }}"
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
            :search-route="route('manajemen.laporan.marketing')"
            search-placeholder="Cari nama marketing..."
            :per-page="0"
            :total="count($laporan)"
            :filtered="count($laporan)"
            :search="request('search')"
            :has-filters="true"
            :exclude-keys="['periode_mulai', 'periode_selesai']"
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
                </div>
            </x-slot:filters>
        </x-data-table-toolbar>

        <!-- Ringkasan -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Closing</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalClosing) }}</p>
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Nilai Penjualan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($totalNilaiPenjualan, 0, ',', '.') }}</p>
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Komisi</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 012-2h2" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Rata-rata Conversion</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($avgConversionRate, 2) }}%</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Top 5 Marketing -->
        <x-card title="Top 5 Marketing by Closing">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Closing</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Nilai</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(array_slice($laporan, 0, 5) as $i => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-gray-500">
                                    @if($i < 3)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                                            style="background-color: {{ ['#FEF3C7', '#E5E7EB', '#FDE68A'][$i] }}; color: {{ ['#92400E', '#374151', '#92400E'][$i] }};">
                                            {{ $i + 1 }}
                                        </span>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </td>
                                <td class="px-3 py-3 font-medium text-gray-800">{{ $row['nama'] }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ number_format($row['total_closing']) }}</td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($row['total_nilai_penjualan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($row['total_komisi'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Chart: Perbandingan Kinerja -->
        <x-card title="Perbandingan Kinerja Marketing">
            <div class="h-80">
                <canvas id="kinerjaMarketingChart"></canvas>
            </div>
        </x-card>

        <!-- Tabel Kinerja Lengkap -->
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">No</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Prospek</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Booking</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Closing</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Conversion Rate</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Target</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Pencapaian</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Nilai</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($laporan as $i => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-3 py-3 font-medium text-gray-800">{{ $row['nama'] }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ number_format($row['total_prospek']) }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ number_format($row['total_booking']) }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ number_format($row['total_closing']) }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        style="background-color: {{ $row['conversion_rate'] >= 50 ? '#D1FAE5' : ($row['conversion_rate'] >= 25 ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $row['conversion_rate'] >= 50 ? '#065F46' : ($row['conversion_rate'] >= 25 ? '#92400E' : '#991B1B') }};">
                                        {{ number_format($row['conversion_rate'], 2) }}%
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ number_format($row['target_unit']) }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        style="background-color: {{ $row['pencapaian_target'] >= 100 ? '#D1FAE5' : ($row['pencapaian_target'] >= 50 ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $row['pencapaian_target'] >= 100 ? '#065F46' : ($row['pencapaian_target'] >= 50 ? '#92400E' : '#991B1B') }};">
                                        {{ number_format($row['pencapaian_target'], 2) }}%
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($row['total_nilai_penjualan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($row['total_komisi'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data kinerja marketing</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartLabels = @json($chartLabels);
                const chartProspek = @json($chartProspek);
                const chartBooking = @json($chartBooking);
                const chartClosing = @json($chartClosing);

                const barCtx = document.getElementById('kinerjaMarketingChart');
                if (barCtx) {
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [
                                {
                                    label: 'Prospek',
                                    data: chartProspek,
                                    backgroundColor: '#6366F1',
                                    borderRadius: 8,
                                    maxBarThickness: 32,
                                },
                                {
                                    label: 'Booking',
                                    data: chartBooking,
                                    backgroundColor: '#F59E0B',
                                    borderRadius: 8,
                                    maxBarThickness: 32,
                                },
                                {
                                    label: 'Closing',
                                    data: chartClosing,
                                    backgroundColor: '#10B981',
                                    borderRadius: 8,
                                    maxBarThickness: 32,
                                }
                            ]
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
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151',
                                        padding: 16,
                                        usePointStyle: true,
                                        pointStyleWidth: 10,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection