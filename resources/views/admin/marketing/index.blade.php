@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Marketing</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola marketing, target, dan kinerjanya.</p>
        </div>
        <a href="{{ route('admin.marketing.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Marketing
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid gap-5 sm:grid-cols-3">
        <x-card>
            <p class="text-sm text-gray-500">Marketing Aktif</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $active }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Closing Bulan Ini</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $closing }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Rata-rata Conversion</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($averageConversion, 2) }}%</p>
        </x-card>
    </div>

    {{-- Data Table Toolbar --}}
    <x-data-table-toolbar
        search-placeholder="Cari nama, username, email, atau no HP..."
        :search-route="route('admin.marketing.index')"
        :per-page="$perPage"
        :total="$totalAll"
        :filtered="$marketings->total()"
        :search="$search"
        :has-filters="$hasFilters"
        :exclude-keys="['status', 'bulan', 'tahun']"
    >
        <x-slot:filters>
            <div class="min-w-[120px]">
                <label for="bulan" class="mb-1 block text-sm font-medium text-gray-700">Bulan</label>
                <select name="bulan" id="bulan" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $index => $name)
                        @php $m = $index + 1; @endphp
                        <option value="{{ $m }}" {{ (int) $bulan === $m ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[100px]">
                <label for="tahun" class="mb-1 block text-sm font-medium text-gray-700">Tahun</label>
                <select name="tahun" id="tahun" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ (int) $tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[160px]">
                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non_aktif" {{ request('status') === 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
        </x-slot:filters>
    </x-data-table-toolbar>

    {{-- Data Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 w-12">No</th>
                        <th class="px-4 py-3">Foto</th>
                        <x-sort-header column="nama_lengkap" label="Nama" :sort-by="$sortBy" :sort-dir="$sortDir" />
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Prospek</th>
                        <th class="px-4 py-3">Booking</th>
                        <th class="px-4 py-3">Closing</th>
                        <th class="px-4 py-3">Target</th>
                        <th class="px-4 py-3">Pencapaian</th>
                        <th class="px-4 py-3">Komisi</th>
                        <x-sort-header column="status" label="Status" :sort-by="$sortBy" :sort-dir="$sortDir" />
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($marketings as $marketing)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-4 py-4 text-gray-600">{{ $marketings->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                @if($marketing->foto_profil)
                                    <img src="{{ asset('storage/' . $marketing->foto_profil) }}" alt="{{ $marketing->nama_lengkap }}" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">{{ strtoupper(mb_substr($marketing->nama_lengkap, 0, 1)) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-semibold text-gray-900">{{ $marketing->nama_lengkap }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $marketing->no_hp ?: '-' }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $marketing->kinerja['prospek'] }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $marketing->kinerja['booking'] }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $marketing->kinerja['closing'] }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $marketing->kinerja['target_unit'] ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <div class="h-2 w-20 overflow-hidden rounded bg-gray-200">
                                    <div class="h-full bg-primary" style="width: {{ min(100, $marketing->kinerja['pencapaian_target']) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ number_format($marketing->kinerja['pencapaian_target'], 0) }}%</span>
                            </td>
                            <td class="px-4 py-4 text-gray-700">Rp {{ number_format($marketing->kinerja['total_komisi'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4"><x-badge :status="$marketing->status" /></td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.marketing.show', $marketing) }}" class="font-semibold text-info transition hover:text-indigo-800" title="Detail">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.marketing.edit', $marketing) }}" class="font-semibold text-primary transition hover:text-primary-dark" title="Edit">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    @if($marketing->status === 'aktif')
                                        <x-confirm-delete :route="route('admin.marketing.destroy', $marketing)" :item-name="$marketing->nama_lengkap" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12">
                                <x-empty-state
                                    title="Belum ada data"
                                    message="Silakan tambahkan marketing baru atau sesuaikan filter pencarian Anda."
                                    :search="$search"
                                    :create-route="route('admin.marketing.create')"
                                    create-label="Tambah Marketing"
                                    :reset-route="route('admin.marketing.index')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>
            <x-pagination :paginator="$marketings" />
        </x-slot:footer>
    </x-card>
</div>
@endsection
