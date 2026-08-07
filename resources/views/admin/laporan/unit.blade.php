@extends('layouts.app')

@section('title', 'Laporan Ketersediaan Unit')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Ketersediaan Unit</h1>
                <p class="text-sm text-gray-500">Status ketersediaan unit per perumahan</p>
            </div>
        </div>

        <x-data-table-toolbar
            :search-route="route('admin.laporan.unit')"
            search-placeholder="Cari laporan unit..."
            :per-page="0"
            :total="count($laporan)"
            :filtered="count($laporan)"
            :search="request('search')"
            :has-filters="true"
            :exclude-keys="['id_perumahan', 'kategori', 'status_unit']"
        >
            <x-slot:filters>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                    <div class="min-w-[200px]">
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
                        <label for="status_unit" class="mb-1 block text-sm font-medium text-gray-700">Status Unit</label>
                        <select name="status_unit" id="status_unit"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ ($filters['status_unit'] ?? '') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="dibooking" {{ ($filters['status_unit'] ?? '') === 'dibooking' ? 'selected' : '' }}>Dibooking</option>
                            <option value="dijual" {{ ($filters['status_unit'] ?? '') === 'dijual' ? 'selected' : '' }}>Terjual</option>
                            <option value="dibatalkan" {{ ($filters['status_unit'] ?? '') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </x-slot:filters>
        </x-data-table-toolbar>

        <!-- Ringkasan -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Unit</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalUnit) }}</p>
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
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tersedia</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalTersedia) }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dibooking</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalDibooking) }}</p>
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Terjual</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalDijual) }}</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dibatalkan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalDibatalkan) }}</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-3 text-red-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Chart: Status Unit -->
        <x-card title="Distribusi Status Unit">
            <div class="h-80">
                <canvas id="statusUnitChart"></canvas>
            </div>
        </x-card>

        <!-- Tabel Unit per Perumahan -->
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">No</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Perumahan</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Total Unit</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Tersedia</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Dibooking</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Terjual</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-500">Dibatalkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($laporan as $i => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-3 py-3 font-medium text-gray-800">{{ $row['nama_perumahan'] }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ number_format($row['total_unit']) }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        style="background-color: #D1FAE5; color: #065F46;">
                                        {{ number_format($row['tersedia']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        style="background-color: #FEF3C7; color: #92400E;">
                                        {{ number_format($row['dibooking']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        style="background-color: #DBEAFE; color: #1E40AF;">
                                        {{ number_format($row['dijual']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        style="background-color: #FEE2E2; color: #991B1B;">
                                        {{ number_format($row['dibatalkan']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data unit</td>
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
                const chartStatusPie = @json($chartStatusPie);

                const pieCtx = document.getElementById('statusUnitChart');
                if (pieCtx) {
                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: chartStatusPie.labels,
                            datasets: [{
                                data: chartStatusPie.data,
                                backgroundColor: ['#10B981', '#F59E0B', '#2563EB', '#EF4444'],
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