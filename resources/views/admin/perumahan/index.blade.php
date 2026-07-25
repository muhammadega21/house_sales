@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Perumahan</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola kawasan perumahan dan ketersediaan unit rumah.</p>
            </div>
            <a href="{{ route('admin.perumahan.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Perumahan
            </a>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-5 sm:grid-cols-3">
            <x-card>
                <p class="text-sm text-gray-500">Perumahan Aktif</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['aktif'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Unit Tersedia</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['tersedia'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Unit Terjual</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['dijual'] }}</p>
            </x-card>
        </div>

        {{-- Data Table Toolbar --}}
        <x-data-table-toolbar search-placeholder="Cari perumahan berdasarkan nama atau kota..." :search-route="route('admin.perumahan.index')"
            :per-page="$perPage" :total="$totalAll" :filtered="$perumahan->total()" :search="$search" :has-filters="$hasFilters" :exclude-keys="['status', 'provinsi']">
            <x-slot:filters>
                <div class="min-w-[160px]">
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non_aktif" {{ request('status') === 'non_aktif' ? 'selected' : '' }}>Non-Aktif
                        </option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label for="provinsi" class="mb-1 block text-sm font-medium text-gray-700">Provinsi</label>
                    <select name="provinsi" id="provinsi" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Provinsi</option>
                        @foreach ($provinsi as $prov)
                            <option value="{{ $prov }}" {{ request('provinsi') === $prov ? 'selected' : '' }}>
                                {{ $prov }}</option>
                        @endforeach
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
                            <x-sort-header column="nama_perumahan" label="Nama" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-sort-header column="kota" label="Kota" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-sort-header column="provinsi" label="Provinsi" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th class="px-4 py-3">Total Unit</th>
                            <x-sort-header column="status" label="Status" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($perumahan as $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $perumahan->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4">
                                    @if ($item->foto_kawasan)
                                        <img src="{{ asset('storage/' . $item->foto_kawasan) }}"
                                            alt="{{ $item->nama_perumahan }}" class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <span
                                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">Tidak
                                            ada foto</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-semibold text-gray-900">{{ $item->nama_perumahan }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $item->kota }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $item->provinsi }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $item->unit_rumah_count }}</td>
                                <td class="px-4 py-4"><x-badge :status="$item->status" /></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.perumahan.show', $item) }}"
                                            class="font-semibold text-info transition hover:text-indigo-800" title="Detail">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.perumahan.edit', $item) }}"
                                            class="font-semibold text-primary transition hover:text-primary-dark"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-confirm-delete :route="route('admin.perumahan.destroy', $item)" :item-name="$item->nama_perumahan" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <x-empty-state title="Belum ada data"
                                        message="Silakan tambahkan perumahan baru atau sesuaikan filter pencarian Anda."
                                        :search="$search" :create-route="route('admin.perumahan.create')" create-label="Tambah Perumahan"
                                        :reset-route="route('admin.perumahan.index')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-slot:footer>
                <x-pagination :paginator="$perumahan" />
            </x-slot:footer>
        </x-card>
    </div>
@endsection
