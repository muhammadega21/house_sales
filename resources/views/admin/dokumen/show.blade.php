@extends('layouts.app')

@section('title', 'Detail Dokumen')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Dokumen</h1>
                <p class="text-sm text-gray-500">Informasi lengkap dan preview dokumen KPR.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($document->status_verifikasi === 'belum_diverifikasi')
                    <a href="{{ route('admin.dokumen.verifikasi', $document->id) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">Verifikasi</a>
                @endif
                <a href="{{ route('admin.dokumen.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Kembali</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Konsumen</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $document->konsumen?->nama_lengkap ?? '-' }}</p>
                            <p class="text-sm text-gray-500">NIK: {{ $document->konsumen?->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Marketing</p>
                            <p class="mt-1 font-semibold text-gray-900">
                                {{ $document->konsumen?->marketing?->nama_lengkap ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3 mt-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Jenis Dokumen</p>
                            <p class="mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->jenis_dokumen)) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Status Verifikasi</p>
                            <x-badge :status="$document->status_verifikasi" />
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Tanggal Upload</p>
                            <p class="mt-1 text-gray-900">{{ $document->tanggal_upload?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3 mt-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Ukuran File</p>
                            <p class="mt-1 text-gray-900">
                                {{ $document->ukuran_file ? number_format($document->ukuran_file / 1024, 1) . ' KB' : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Tipe File</p>
                            <p class="mt-1 text-gray-900">{{ strtoupper($document->tipe_file ?? 'PDF/JPG') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Tanggal Verifikasi</p>
                            <p class="mt-1 text-gray-900">{{ $document->tanggal_verifikasi?->format('d/m/Y H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    @if ($document->catatan_verifikasi)
                        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm font-semibold text-gray-900">Catatan Verifikasi</p>
                            <p class="mt-2 text-sm text-gray-700">{{ $document->catatan_verifikasi }}</p>
                        </div>
                    @endif
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Preview Dokumen</h2>
                            <p class="text-sm text-gray-500">Zoom dan lihat file secara keseluruhan.</p>
                        </div>
                        <a href="{{ asset('storage/' . $document->path_file) }}" target="_blank"
                            class="text-sm font-semibold text-primary hover:text-primary-dark">Buka di Tab Baru</a>
                    </div>

                    @if (in_array(strtolower($document->tipe_file), ['jpg', 'jpeg', 'png'], true))
                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-black p-4">
                            <img src="{{ asset('storage/' . $document->path_file) }}" alt="Preview dokumen"
                                class="h-full w-full object-contain" />
                        </div>
                    @else
                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-900">
                            <iframe src="{{ asset('storage/' . $document->path_file) }}" class="h-[650px] w-full"
                                frameborder="0"></iframe>
                        </div>
                    @endif
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Konsumen Lainnya</h2>
                    @if ($document->konsumen?->dokumenKpr->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($document->konsumen->dokumenKpr as $other)
                                @if ($other->id !== $document->id)
                                    <div class="rounded-2xl border border-gray-200 bg-white p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ ucfirst(str_replace('_', ' ', $other->jenis_dokumen)) }}</p>
                                                <p class="text-sm text-gray-500">{{ $other->nama_file }}</p>
                                            </div>
                                            <x-badge :status="$other->status_verifikasi" />
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Tidak ada dokumen lain.</p>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
@endsection
