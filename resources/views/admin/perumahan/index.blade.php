@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold text-gray-900">Data Perumahan</h1><p class="mt-1 text-sm text-gray-500">Kelola kawasan perumahan dan ketersediaan unit rumah.</p></div>
        <a href="{{ route('admin.perumahan.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark"><span aria-hidden="true">+</span>Tambah Perumahan</a>
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <x-card><p class="text-sm text-gray-500">Perumahan Aktif</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['aktif'] }}</p></x-card>
        <x-card><p class="text-sm text-gray-500">Unit Tersedia</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['tersedia'] }}</p></x-card>
        <x-card><p class="text-sm text-gray-500">Unit Terjual</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['dijual'] }}</p></x-card>
    </div>

    <x-card>
        <form method="GET" action="{{ route('admin.perumahan.index') }}" class="grid gap-4 md:grid-cols-4 md:items-end">
            <x-form-input name="search" label="Cari Perumahan" placeholder="Nama perumahan atau kota" :value="request('search')" />
            <x-form-select name="status" label="Status" :options="['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif']" :selected="request('status')" />
            <x-form-select name="provinsi" label="Provinsi" :options="$provinsi->mapWithKeys(fn ($item) => [$item => $item])->all()" :selected="request('provinsi')" />
            <div class="mb-4 flex gap-2"><button type="submit" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark">Terapkan</button>@if(request()->hasAny(['search', 'status', 'provinsi']))<a href="{{ route('admin.perumahan.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>@endif</div>
        </form>
    </x-card>

    <x-card>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"><tr><th class="px-4 py-3">No</th><th class="px-4 py-3">Foto</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Kota</th><th class="px-4 py-3">Provinsi</th><th class="px-4 py-3">Total Unit</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($perumahan as $item)
                    <tr class="transition hover:bg-gray-50"><td class="px-4 py-4 text-gray-600">{{ $perumahan->firstItem() + $loop->index }}</td><td class="px-4 py-4">@if($item->foto_kawasan)<img src="{{ asset('storage/' . $item->foto_kawasan) }}" alt="{{ $item->nama_perumahan }}" class="h-12 w-16 rounded-lg object-cover">@else<span class="flex h-12 w-16 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">Tidak ada foto</span>@endif</td><td class="px-4 py-4 font-semibold text-gray-900">{{ $item->nama_perumahan }}</td><td class="px-4 py-4 text-gray-700">{{ $item->kota }}</td><td class="px-4 py-4 text-gray-700">{{ $item->provinsi }}</td><td class="px-4 py-4 text-gray-700">{{ $item->unit_rumah_count }}</td><td class="px-4 py-4"><x-badge :status="$item->status" /></td><td class="px-4 py-4 text-right"><div class="inline-flex items-center gap-3"><a href="{{ route('admin.perumahan.show', $item) }}" class="font-semibold text-info transition hover:text-indigo-800">Detail</a><a href="{{ route('admin.perumahan.edit', $item) }}" class="font-semibold text-primary transition hover:text-primary-dark">Edit</a><x-confirm-delete :route="route('admin.perumahan.destroy', $item)" :item-name="$item->nama_perumahan" /></div></td></tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-500">Data perumahan tidak ditemukan.</td></tr>
                @endforelse
            </tbody></table></div>
        <x-slot:footer><x-pagination :paginator="$perumahan" /></x-slot:footer>
    </x-card>
</div>
@endsection