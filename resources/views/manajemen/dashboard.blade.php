@extends('layouts.app')

@section('title', 'Dashboard Manajemen')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Manajemen</h1>
                <p class="text-sm text-gray-500">Selamat datang kembali, {{ auth()->user()->nama_lengkap }}</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Perumahan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalPerumahan }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0v-5a2 2 0 012-2h2a2 2 0 012 2v5m-6 0h6" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Unit Terjual / Total Unit
                        </p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalTerjual }} / {{ $totalUnit }}</p>
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
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalBookingBulanIni }}</p>
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
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Omset Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp
                            {{ number_format($totalOmsetBulanIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-slate-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Widgets -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-card title="Conversion Rate Perusahaan" subtitle="Prospek menjadi closing bulan ini">
                <div class="flex items-center justify-between">
                    <p class="text-4xl font-bold text-gray-900">{{ $conversionRatePerusahaan }}%</p>
                    <div class="rounded-full px-4 py-2 text-sm font-semibold"
                        style="background-color: {{ $conversionRatePerusahaan >= 50 ? '#D1FAE5' : ($conversionRatePerusahaan >= 25 ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $conversionRatePerusahaan >= 50 ? '#065F46' : ($conversionRatePerusahaan >= 25 ? '#92400E' : '#991B1B') }};">
                        {{ $conversionRatePerusahaan >= 50 ? 'Baik' : ($conversionRatePerusahaan >= 25 ? 'Cukup' : 'Perlu Perbaikan') }}
                    </div>
                </div>
            </x-card>

            <x-card title="Rata-rata Waktu Closing" subtitle="Rata-rata hari dari booking hingga akad">
                <div class="flex items-center justify-between">
                    <p class="text-4xl font-bold text-gray-900">{{ $rataRataWaktuClosing }} hari</p>
                    <div class="rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-800">Stabil</div>
                </div>
            </x-card>

            <x-card title="Dokumen Pending Verifikasi" subtitle="Menunggu verifikasi admin">
                <div class="flex items-center justify-between">
                    <p class="text-4xl font-bold text-gray-900">{{ $dokumenPending }}</p>
                    <div class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-800">
                        {{ $dokumenPending > 0 ? 'Perlu Ditindak' : 'Tidak Ada' }}
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-card title="Penjualan per Bulan">
                <div class="h-72">
                    <canvas id="penjualanPerBulanChart"></canvas>
                </div>
            </x-card>

            <x-card title="Subsidi vs Non-Subsidi">
                <div class="h-72">
                    <canvas id="kategoriBreakdownChart"></canvas>
                </div>
            </x-card>

            <x-card title="Tren Penjualan">
                <div class="h-72">
                    <canvas id="trenPenjualanChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Tables -->
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <x-card title="Top 5 Marketing by Nilai Penjualan">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Closing</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($top5Marketing as $index => $marketing)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 font-medium text-gray-800">{{ $marketing->nama_lengkap }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $marketing->total_closing }}</td>
                                    <td class="px-3 py-3 text-gray-600">Rp
                                        {{ number_format($marketing->total_nilai_penjualan, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Booking Terbaru">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Konsumen</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal
                                    Booking</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($latestBookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-gray-600">{{ $booking->kode_booking }}</td>
                                    <td class="px-3 py-3 text-gray-800">{{ $booking->nama_konsumen ?? '-' }}</td>
                                    <td class="px-3 py-3 text-gray-600">
                                        {{ optional($booking->tanggal_booking)->format('d M Y') ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <x-badge :status="$booking->status_penjualan ?? 'booking'" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada
                                        booking terbaru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Unit Tersedia per Perumahan">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Perumahan
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tersedia
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Total Unit
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($unitsAvailableByPerumahan as $unit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-gray-800">{{ $unit->nama_perumahan }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $unit->tersedia }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $unit->total_unit }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data
                                        unit</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const penjualanPerBulan = @json($penjualanPerBulan);
                const kategoriBreakdown = @json($kategoriBreakdown);

                const barCtx = document.getElementById('penjualanPerBulanChart');
                if (barCtx) {
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: penjualanPerBulan.map(item => item.label),
                            datasets: [{
                                label: 'Total Omset',
                                data: penjualanPerBulan.map(item => item.total_nilai),
                                backgroundColor: '#2563EB',
                                borderRadius: 12,
                                maxBarThickness: 32,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            maximumFractionDigits: 0
                                        }).format(value),
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
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                const pieCtx = document.getElementById('kategoriBreakdownChart');
                if (pieCtx) {
                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Subsidi', 'Non-Subsidi'],
                            datasets: [{
                                data: [kategoriBreakdown.subsidi.total_nilai, kategoriBreakdown
                                    .non_subsidi.total_nilai
                                ],
                                backgroundColor: ['#10B981', '#2563EB'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151'
                                    }
                                }
                            }
                        }
                    });
                }

                const lineCtx = document.getElementById('trenPenjualanChart');
                if (lineCtx) {
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: penjualanPerBulan.map(item => item.label),
                            datasets: [{
                                label: 'Unit Terjual',
                                data: penjualanPerBulan.map(item => item.total_unit),
                                borderColor: '#2563EB',
                                backgroundColor: 'rgba(37, 99, 235, 0.15)',
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
                                        color: '#374151'
                                    },
                                    grid: {
                                        color: 'rgba(15, 23, 42, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#374151'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151'
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