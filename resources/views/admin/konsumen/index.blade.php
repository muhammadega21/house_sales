@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Konsumen</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola seluruh data konsumen.</p>
            </div>
            <a href="{{ route('admin.konsumen.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Konsumen
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <x-card>
                <p class="text-sm text-gray-500">Total Konsumen</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Dengan Booking Aktif</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['with_booking'] ?? 0 }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Dengan KPR Aktif</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['with_kpr'] ?? 0 }}</p>
            </x-card>
        </div>

        <x-data-table-toolbar
            search-placeholder="Cari nama, NIK, atau no HP..."
            :search-route="route('admin.konsumen.index')"
            :per-page="$perPage ?? 10"
            :total="0"
            :filtered="0"
            :search="$search ?? ''"
            :has-filters="$hasFilters ?? false"
        >
            <x-slot:filters>
                <div class="min-w-[160px]">
                    <label for="id_marketing" class="mb-1 block text-sm font-medium text-gray-700">Marketing PIC</label>
                    <select name="id_marketing" id="id_marketing" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua Marketing</option>
                        @foreach($marketingOptions as $id => $nama)
                            <option value="{{ $id }}" {{ request('id_marketing') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </x-slot:filters>
        </x-data-table-toolbar>

        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIK</th>
                            <th class="px-4 py-3">No HP</th>
                            <th class="px-4 py-3">Pekerjaan</th>
                            <th class="px-4 py-3">Jml Booking</th>
                            <th class="px-4 py-3">Marketing PIC</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($konsumens as $konsumen)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $konsumens->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4 font-semibold text-gray-900">{{ $konsumen->nama_lengkap }}</td>
                                <td class="px-4 py-4 text-gray-700 font-mono text-xs">{{ $konsumen->nik }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $konsumen->no_hp }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $konsumen->pekerjaan ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $konsumen->total_bookings ?? 0 }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $konsumen->marketing?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.konsumen.show', $konsumen->id) }}"
                                            class="font-semibold text-info transition hover:text-indigo-800" title="Detail">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.konsumen.edit', $konsumen->id) }}"
                                            class="font-semibold text-primary transition hover:text-primary-dark" title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-confirm-delete :route="route('admin.konsumen.destroy', $konsumen->id)" :item-name="$konsumen->nama_lengkap" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <x-empty-state title="Belum ada konsumen"
                                        message="Silakan tambahkan konsumen baru atau sesuaikan filter pencarian Anda."
                                        :search="$search ?? ''" :create-route="route('admin.konsumen.create')"
                                        create-label="Tambah Konsumen" :reset-route="route('admin.konsumen.index')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <x-pagination :paginator="$konsumens" />
            </div>
        </x-card>
    </div>
@endsection