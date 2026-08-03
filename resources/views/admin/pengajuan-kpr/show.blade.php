@extends('layouts.app')

@section('title', 'Detail Pengajuan KPR')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan KPR</h1>
                <p class="text-sm text-gray-500">Kode #{{ str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <a href="{{ route('admin.pengajuan-kpr.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Pengajuan Info -->
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
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Keputusan</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tanggal_keputusan?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</p>
                            <p class="mt-1"><x-badge :status="$pengajuan->status_pengajuan" /></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if ($pengajuan->catatan)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $pengajuan->catatan }}</p>
                        </div>
                    @endif
                </x-card>

                <!-- Status History -->
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Status Penjualan</h2>
                    @if ($pengajuan->booking?->statusHistory->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($pengajuan->booking->statusHistory->sortByDesc('id') as $history)
                                <div class="flex items-start gap-3 text-sm">
                                    <div class="mt-1 h-2 w-2 rounded-full bg-gray-400"></div>
                                    <div>
                                        <span class="font-medium text-gray-900">
                                            {{ \App\Enums\StatusPenjualan::tryFrom($history->status_sebelum)?->label() ?? $history->status_sebelum }}
                                        </span>
                                        <span class="text-gray-400 mx-2">→</span>
                                        <span class="font-medium text-gray-900">
                                            {{ \App\Enums\StatusPenjualan::tryFrom($history->status_sesudah)?->label() ?? $history->status_sesudah }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-400">
                                            {{ $history->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        @if ($history->catatan)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $history->catatan }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Belum ada riwayat status.</p>
                    @endif
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Konsumen Info -->
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Konsumen</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">NIK</p>
                            <p class="mt-1 font-mono text-gray-700">{{ $pengajuan->konsumen?->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">No HP</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->no_hp ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->email ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Unit Info -->
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Unit</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Unit</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->unit?->kode_unit ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tipe</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->unit?->tipe_rumah ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Perumahan</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->unit?->perumahan?->nama_perumahan ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Harga Jual</p>
                            <p class="mt-1 text-gray-900">Rp {{ number_format($pengajuan->unit?->harga_jual ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Update Status -->
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Ubah Status</h2>
                    <form method="POST" action="{{ route('admin.pengajuan-kpr.update-status', $pengajuan->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="status_pengajuan" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status_pengajuan" id="status_pengajuan"
                                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                                    <option value="">Pilih status</option>
                                    <option value="draft" {{ $pengajuan->status_pengajuan === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="diajukan" {{ $pengajuan->status_pengajuan === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                    <option value="verifikasi_bank" {{ $pengajuan->status_pengajuan === 'verifikasi_bank' ? 'selected' : '' }}>Verifikasi Bank</option>
                                    <option value="disetujui" {{ $pengajuan->status_pengajuan === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ $pengajuan->status_pengajuan === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="akad" {{ $pengajuan->status_pengajuan === 'akad' ? 'selected' : '' }}>Akad</option>
                                    <option value="batal" {{ $pengajuan->status_pengajuan === 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                                @error('status_pengajuan')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <textarea name="catatan" id="catatan" rows="3"
                                    class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                    placeholder="Tambahkan catatan...">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                Perbarui Status
                            </button>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>
@endsection