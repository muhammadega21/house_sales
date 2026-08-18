@extends('layouts.app')

@section('title', 'Kinerja Saya')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kinerja Saya</h1>
                <p class="text-sm text-gray-500">Pantau performa penjualan Anda</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Prospek Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalProspek) }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalBooking) }}</p>
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Closing Bulan Ini</p>
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Conversion Rate</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($conversionRate, 2) }}%</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Komisi Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3 text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Target vs Realisasi -->
        <x-card title="Target vs Realisasi Bulan Ini">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Pencapaian Target</span>
                    <span class="text-sm font-bold text-gray-800">{{ number_format($progressTarget, 1) }}%</span>
                </div>
                <div class="h-5 w-full rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                        style="width: {{ min($progressTarget, 100) }}%; background-color: {{ $progressTarget >= 100 ? '#10B981' : ($progressTarget >= 50 ? '#F59E0B' : '#EF4444') }};">
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>Target: {{ number_format($targetUnit) }} unit</span>
                    <span>Realisasi: {{ number_format($totalClosing) }} unit</span>
                </div>
            </div>
        </x-card>

        <!-- Filter -->
        <x-card>
            <form method="GET" action="{{ route('marketing.kinerja.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="min-w-[160px]">
                    <label for="bulan" class="mb-1 block text-sm font-medium text-gray-700">Bulan</label>
                    <select name="bulan" id="bulan"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $start->month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, null)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label for="tahun" class="mb-1 block text-sm font-medium text-gray-700">Tahun</label>
                    <select name="tahun" id="tahun"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @foreach(range(date('Y'), date('Y') - 2, -1) as $y)
                            <option value="{{ $y }}" {{ date('Y', strtotime($start)) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                        <svg class="mr-1 -ml-0.5 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Terapkan
                    </button>
                </div>
            </form>
        </x-card>

        <!-- Charts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Line Chart: Tren Prospek vs Closing -->
            <x-card title="Tren Prospek vs Closing (6 Bulan)">
                <div class="h-80">
                    <canvas id="trenKinerjaChart"></canvas>
                </div>
            </x-card>

            <!-- Pie Chart: Prospek per Sumber -->
            <x-card title="Prospek per Sumber">
                <div class="h-80">
                    <canvas id="prospekPerSumberChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Tabel Riwayat Closing -->
        <x-card>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Riwayat Closing</h2>
                <p class="text-sm text-gray-500">Daftar transaksi akad yang berhasil</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">No</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Konsumen</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unit</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal Akad</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Nilai</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Status</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-gray-500">Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($closingRows as $i => $row)
                            @php
                                $komisi = (float) $row->harga_jual * ((float) ($persentaseKomisi ?? 0) / 100);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-gray-500">{{ $closingRows->firstItem() + $i }}</td>
                                <td class="px-3 py-3 font-medium text-gray-800">{{ $row->nama_konsumen }}</td>
                                <td class="px-3 py-3 text-gray-600">{{ $row->kode_unit }}</td>
                                <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($row->tanggal_perubahan)->format('d M Y') }}</td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format((float) $row->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-center">
                                    <x-badge :status="$row->status_saat_ini" />
                                </td>
                                <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($komisi, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada riwayat closing</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <x-pagination :paginator="$closingRows" />
            </div>
        </x-card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartLabels = @json($chartLabels);
                const chartProspek = @json($chartProspek);
                const chartClosing = @json($chartClosing);
                const sumberProspek = @json($sumberProspek);

                const lineCtx = document.getElementById('trenKinerjaChart');
                if (lineCtx) {
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Prospek',
                                data: chartProspek,
                                borderColor: '#2563EB',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }, {
                                label: 'Closing',
                                data: chartClosing,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointHoverRadius: 6,
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
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }

                const pieCtx = document.getElementById('prospekPerSumberChart');
                if (pieCtx) {
                    const sumberLabels = sumberProspek.map(item => item.label);
                    const sumberData = sumberProspek.map(item => item.total);
                    const sumberColors = [
                        '#2563EB', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#6B7280'
                    ];

                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: sumberLabels,
                            datasets: [{
                                data: sumberData,
                                backgroundColor: sumberColors.slice(0, sumberLabels.length),
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
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