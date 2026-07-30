@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Prospek</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola semua prospek perusahaan.</p>
        </div>
        <a href="{{ route('admin.prospek.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Prospek
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <x-card>
            <p class="text-sm text-gray-500">Total Prospek</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        </x-card>
        @foreach(\App\Enums\StatusProspek::cases() as $status)
        <x-card>
            <p class="text-sm text-gray-500">{{ $status->label() }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats[$status->value] ?? 0 }}</p>
        </x-card>
        @endforeach
    </div>

    {{-- Data Table Toolbar --}}
    @php
        $marketingOptions = \App\Models\User::marketing()->aktif()->orderBy('nama_lengkap')->get()->mapWithKeys(fn($m) => [$m->id => $m->nama_lengkap]);
    @endphp
    <x-data-table-toolbar
        search-placeholder="Cari nama, no HP, atau marketing..."
        :search-route="route('admin.prospek.index')"
        :per-page="$perPage"
        :total="$stats['total'] ?? 0"
        :filtered="$prospeks->total()"
        :search="$search"
        :has-filters="$hasFilters"
        :exclude-keys="['status_prospek', 'sumber_prospek', 'id_marketing']"
    >
        <x-slot:filters>
            <div class="min-w-[140px]">
                <label for="status_prospek" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status_prospek" id="status_prospek" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusProspek::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status_prospek') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label for="sumber_prospek" class="mb-1 block text-sm font-medium text-gray-700">Sumber</label>
                <select name="sumber_prospek" id="sumber_prospek" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Sumber</option>
                    @foreach(\App\Enums\SumberProspek::cases() as $sumber)
                        <option value="{{ $sumber->value }}" {{ request('sumber_prospek') === $sumber->value ? 'selected' : '' }}>
                            {{ $sumber->icon() }} {{ $sumber->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[160px]">
                <label for="id_marketing" class="mb-1 block text-sm font-medium text-gray-700">Marketing</label>
                <select name="id_marketing" id="id_marketing" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Marketing</option>
                    @foreach($marketingOptions as $id => $nama)
                        <option value="{{ $id }}" {{ request('id_marketing') == $id ? 'selected' : '' }}>{{ $nama }}</option>
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
                        <th class="px-4 py-3">Nama Prospek</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Sumber</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Marketing PIC</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($prospeks as $prospek)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-4 py-4 text-gray-600">{{ $prospeks->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4 font-semibold text-gray-900">{{ $prospek->nama_prospek }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $prospek->no_hp }}</td>
                            <td class="px-4 py-4">
                                @php $sumber = $prospek->sumber_prospek; @endphp
                                @if($sumber)
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 border border-gray-200">
                                        <span>{{ $sumber->icon() }}</span>
                                        <span>{{ $sumber->label() }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $status = $prospek->status_prospek;
                                    $statusColor = match($status->value) {
                                        'baru' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'dihubungi' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'berminat' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'tidak_berminat' => 'bg-red-100 text-red-800 border-red-200',
                                        'jadi_konsumen' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border {{ $statusColor }}">
                                    {{ $status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-700 whitespace-nowrap">{{ $prospek->tanggal_prospek->format('d/m/Y') }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $prospek->marketing->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.prospek.show', $prospek->id) }}" class="font-semibold text-info transition hover:text-indigo-800" title="Detail">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.prospek.edit', $prospek->id) }}" class="font-semibold text-primary transition hover:text-primary-dark" title="Edit">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.prospek.convert', $prospek->id) }}" class="font-semibold text-emerald-600 transition hover:text-emerald-800" title="Konversi">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
                                    </a>
                                    @if($prospek->status_prospek->value !== 'jadi_konsumen')
                                        <x-confirm-delete :route="route('admin.prospek.destroy', $prospek->id)" :item-name="$prospek->nama_prospek" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state
                                    title="Belum ada prospek"
                                    message="Silakan tambahkan prospek baru atau sesuaikan filter pencarian Anda."
                                    :search="$search"
                                    :create-route="route('admin.prospek.create')"
                                    create-label="Tambah Prospek"
                                    :reset-route="route('admin.prospek.index')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <x-pagination :paginator="$prospeks" />
        </div>
    </x-card>
</div>
@endsection
