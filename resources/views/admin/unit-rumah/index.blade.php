@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Unit Rumah</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola unit rumah dan ketersediaannya.</p>
            </div>
            <a href="{{ route('admin.unit-rumah.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Unit
            </a>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <p class="text-sm text-gray-500">Unit Tersedia</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $summary['tersedia'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Unit Di-booking</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $summary['dibooking'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Unit Terjual</p>
                <p class="mt-1 text-2xl font-bold text-blue-600">{{ $summary['dijual'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Unit Batal</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ $summary['dibatalkan'] }}</p>
            </x-card>
        </div>

        {{-- Data Table Toolbar --}}
        <x-data-table-toolbar search-placeholder="Cari kode unit atau tipe rumah..." :search-route="route('admin.unit-rumah.index')" :per-page="$perPage"
            :total="$totalAll" :filtered="$units->total()" :search="$search" :has-filters="$hasFilters" :exclude-keys="['id_perumahan', 'kategori', 'status_unit', 'jenis_ketersediaan']">
            <x-slot:filters>
                <div class="min-w-[160px]">
                    <label for="id_perumahan" class="mb-1 block text-sm font-medium text-gray-700">Perumahan</label>
                    <select name="id_perumahan" id="id_perumahan" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Perumahan</option>
                        @foreach ($perumahan as $id => $nama)
                            <option value="{{ $id }}" {{ request('id_perumahan') == $id ? 'selected' : '' }}>
                                {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="kategori" class="mb-1 block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="kategori" id="kategori" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Kategori</option>
                        <option value="subsidi" {{ request('kategori') === 'subsidi' ? 'selected' : '' }}>Subsidi</option>
                        <option value="non_subsidi" {{ request('kategori') === 'non_subsidi' ? 'selected' : '' }}>
                            Non-Subsidi</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="status_unit" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status_unit" id="status_unit" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status_unit') === 'tersedia' ? 'selected' : '' }}>Tersedia
                        </option>
                        <option value="dibooking" {{ request('status_unit') === 'dibooking' ? 'selected' : '' }}>Di-booking
                        </option>
                        <option value="dijual" {{ request('status_unit') === 'dijual' ? 'selected' : '' }}>Dijual</option>
                        <option value="dibatalkan" {{ request('status_unit') === 'dibatalkan' ? 'selected' : '' }}>
                            Dibatalkan</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="jenis_ketersediaan"
                        class="mb-1 block text-sm font-medium text-gray-700">Ketersediaan</label>
                    <select name="jenis_ketersediaan" id="jenis_ketersediaan" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        <option value="ready_stock"
                            {{ request('jenis_ketersediaan') === 'ready_stock' ? 'selected' : '' }}>Ready Stock</option>
                        <option value="indent" {{ request('jenis_ketersediaan') === 'indent' ? 'selected' : '' }}>Indent
                        </option>
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
                            <x-sort-header column="kode_unit" label="Kode Unit" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th class="px-4 py-3">Perumahan</th>
                            <x-sort-header column="tipe_rumah" label="Tipe" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-sort-header column="kategori" label="Kategori" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-sort-header column="luas_tanah" label="Luas T/B" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-sort-header column="harga_jual" label="Harga" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-sort-header column="status_unit" label="Status" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($units as $unit)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $units->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4">
                                    @if ($unit->foto_unit)
                                        <img src="{{ asset('storage/' . $unit->foto_unit) }}" alt="{{ $unit->kode_unit }}"
                                            class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <span
                                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">Tidak
                                            ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-semibold text-gray-900">{{ $unit->kode_unit }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $unit->perumahan->nama_perumahan }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $unit->tipe_rumah }}</td>
                                <td class="px-4 py-4"><x-badge :status="$unit->kategori->value" /></td>
                                <td class="px-4 py-4 text-gray-700">{{ $unit->luas_tanah }}/{{ $unit->luas_bangunan }} m²
                                </td>
                                <td class="px-4 py-4 text-gray-700">Rp
                                    {{ number_format((float) $unit->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 py-4"><x-badge :status="$unit->status_unit->value" /></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.unit-rumah.show', $unit) }}"
                                            class="font-semibold text-info transition hover:text-indigo-800"
                                            title="Detail">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.unit-rumah.edit', $unit) }}"
                                            class="font-semibold text-primary transition hover:text-primary-dark"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-confirm-delete :route="route('admin.unit-rumah.destroy', $unit)" :item-name="$unit->kode_unit" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <x-empty-state title="Belum ada data"
                                        message="Silakan tambahkan unit baru atau sesuaikan filter pencarian Anda."
                                        :search="$search" :create-route="route('admin.unit-rumah.create')" create-label="Tambah Unit"
                                        :reset-route="route('admin.unit-rumah.index')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-slot:footer>
                <x-pagination :paginator="$units" />
            </x-slot:footer>
        </x-card>
    </div>
@endsection
