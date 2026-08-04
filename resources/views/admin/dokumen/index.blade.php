@extends('layouts.app')

@section('title', 'Verifikasi Dokumen KPR')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Dokumen KPR</h1>
                <p class="text-sm text-gray-500">Kelola semua dokumen KPR untuk marketing dan konsumen.</p>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.dokumen.index', ['status_verifikasi' => 'belum_diverifikasi']) }}"
                class="group block rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:bg-amber-100">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Menunggu Verifikasi</p>
                        <p class="mt-2 text-3xl font-bold text-amber-900">{{ $stats['belum_diverifikasi'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-amber-100 p-3 text-amber-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </a>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Valid Bulan Ini</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $stats['valid_this_month'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-emerald-100 p-3 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-red-700">Perlu Revisi</p>
                        <p class="mt-2 text-3xl font-bold text-red-900">{{ $stats['perlu_revisi'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-red-100 p-3 text-red-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tidak Valid</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['tidak_valid'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-gray-100 p-3 text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <x-card>
            <form method="GET" action="{{ route('admin.dokumen.index') }}" class="grid gap-4 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Nama konsumen, NIK, atau nama file"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label for="status_verifikasi" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status_verifikasi" id="status_verifikasi"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\StatusVerifikasiDokumen::cases() as $status)
                            <option value="{{ $status->value }}" {{ $filterStatus === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="jenis_dokumen" class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen</label>
                    <select name="jenis_dokumen" id="jenis_dokumen"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\JenisDokumen::cases() as $jenis)
                            <option value="{{ $jenis->value }}" {{ $filterJenis === $jenis->value ? 'selected' : '' }}>
                                {{ $jenis->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="id_marketing" class="block text-sm font-medium text-gray-700 mb-1">Marketing</label>
                    <select name="id_marketing" id="id_marketing"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua Marketing</option>
                        @foreach ($marketingOptions as $id => $nama)
                            <option value="{{ $id }}" {{ $filterMarketing == $id ? 'selected' : '' }}>
                                {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tanggal_from" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Upload
                        dari</label>
                    <input type="date" name="tanggal_from" id="tanggal_from" value="{{ $filterTanggalFrom }}"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label for="tanggal_to" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Upload
                        sampai</label>
                    <input type="date" name="tanggal_to" id="tanggal_to" value="{{ $filterTanggalTo }}"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary">
                </div>

                <div class="lg:col-span-6 flex flex-wrap items-end gap-3">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">Filter</button>
                    <a href="{{ route('admin.dokumen.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </x-card>

        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Tanggal Upload</th>
                            <th class="px-4 py-3">Konsumen</th>
                            <th class="px-4 py-3">Marketing</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Nama File</th>
                            <th class="px-4 py-3">Ukuran</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($documents as $document)
                            <tr
                                class="{{ $document->status_verifikasi === 'belum_diverifikasi' ? 'bg-yellow-50' : '' }} transition hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $documents->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4 text-gray-700 whitespace-nowrap">
                                    {{ $document->tanggal_upload?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $document->konsumen?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $document->konsumen?->marketing?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ ucfirst(str_replace('_', ' ', $document->jenis_dokumen)) }}</span>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $document->nama_file }}</td>
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $document->ukuran_file ? number_format($document->ukuran_file / 1024, 1) . ' KB' : '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge :status="$document->status_verifikasi" />
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                        @if ($document->status_verifikasi === 'belum_diverifikasi')
                                            <a href="{{ route('admin.dokumen.verifikasi', $document->id) }}"
                                                class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark">Verifikasi</a>
                                        @endif
                                        <a href="{{ route('admin.dokumen.show', $document->id) }}"
                                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-400">Belum ada dokumen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$documents" />
            </div>
        </x-card>
    </div>
@endsection
