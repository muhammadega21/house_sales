@extends('layouts.app')

@section('title', 'Detail Pengajuan KPR')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan KPR</h1>
                <p class="text-sm text-gray-500">Detail pengajuan untuk booking {{ $pengajuan->booking->kode_booking }}.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('marketing.pengajuan-kpr.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Kembali
                </a>
                <a href="{{ route('marketing.pengajuan-kpr.edit', $pengajuan->id) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengajuan</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Bank</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->nama_bank ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Plafon KPR</p>
                            <p class="mt-1 text-gray-900">Rp {{ number_format($pengajuan->plafon_kpr ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tenor</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tenor_tahun ?? '-' }} tahun</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Suku Bunga</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->suku_bunga ?? '-' }}%</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Pengajuan</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</p>
                            <p class="mt-1"><x-badge :status="$pengajuan->status_pengajuan" /></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $pengajuan->catatan ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Booking</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Kode Booking</p>
                            <p class="font-semibold text-gray-900">{{ $pengajuan->booking->kode_booking }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Konsumen</p>
                            <p class="font-semibold text-gray-900">{{ $pengajuan->booking->konsumen?->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Unit</p>
                            <p class="font-semibold text-gray-900">{{ $pengajuan->booking->unit?->kode_unit ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Harga Unit</p>
                            <p class="font-semibold text-gray-900">Rp {{ number_format($pengajuan->booking->unit?->harga_jual ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Konsumen</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">NIK</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">No HP</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->no_hp ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
@endsection
