@extends('layouts.app')

@section('title', 'Verifikasi Dokumen')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Dokumen</h1>
                <p class="text-sm text-gray-500">Periksa dan verifikasi dokumen konsumen sebelum KPR diajukan.</p>
            </div>
            <a href="{{ route('admin.dokumen.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Kembali</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Konsumen</p>
                                <p class="mt-1 font-semibold text-gray-900">{{ $document->konsumen?->nama_lengkap ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-500">NIK: {{ $document->konsumen?->nik ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Marketing</p>
                                <p class="mt-1 font-semibold text-gray-900">
                                    {{ $document->konsumen?->marketing?->nama_lengkap ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Jenis Dokumen</p>
                                <x-badge :status="$document->jenis_dokumen" />
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Nama File</p>
                                <p class="mt-1 text-gray-900">{{ $document->nama_file }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Ukuran</p>
                                <p class="mt-1 text-gray-900">
                                    {{ $document->ukuran_file ? number_format($document->ukuran_file / 1024, 1) . ' KB' : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Tipe File</p>
                                <p class="mt-1 text-gray-900">{{ strtoupper($document->tipe_file ?? 'PDF/JPG') }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Tanggal Upload</p>
                                <p class="mt-1 text-gray-900">{{ $document->tanggal_upload?->format('d/m/Y H:i') ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Preview Dokumen</h2>
                            <p class="text-sm text-gray-500">Lihat konten dokumen sebelum memutuskan status.</p>
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
                            <iframe src="{{ asset('storage/' . $document->path_file) }}" class="h-[600px] w-full"
                                frameborder="0"></iframe>
                        </div>
                    @endif
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Checklist Kualitas</h2>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">☐</span>
                            <span>Dokumen jelas dan terbaca.</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">☐</span>
                            <span>Data sesuai dengan identitas konsumen.</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">☐</span>
                            <span>Tidak kedaluwarsa / masih valid jika berlaku.</span>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Form Verifikasi</h2>
                    <form method="POST" action="{{ route('admin.dokumen.proses-verifikasi', $document->id) }}"
                        x-data="{ status: '{{ old('status_verifikasi', $document->status_verifikasi === 'belum_diverifikasi' ? '' : $document->status_verifikasi) }}' }">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Status Verifikasi</p>
                                <div class="space-y-3">
                                    @foreach (['valid' => 'Valid', 'tidak_valid' => 'Tidak Valid', 'perlu_revisi' => 'Perlu Revisi'] as $value => $label)
                                        <label
                                            class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer transition hover:border-primary">
                                            <input type="radio" name="status_verifikasi" value="{{ $value }}"
                                                x-model="status"
                                                class="h-4 w-4 text-primary border-gray-300 focus:ring-primary" />
                                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('status_verifikasi')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="catatan_verifikasi"
                                    class="block text-sm font-semibold text-gray-700 mb-1">Catatan Verifikasi</label>
                                <textarea name="catatan_verifikasi" id="catatan_verifikasi" rows="4"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary"
                                    placeholder="Contoh: Slip gaji bulan Maret tidak terbaca, mohon upload ulang">{{ old('catatan_verifikasi') }}</textarea>
                                @error('catatan_verifikasi')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500"
                                    x-text="status === 'tidak_valid' ? 'Contoh: KTP bukan milik konsumen' : (status === 'perlu_revisi' ? 'Contoh: Slip gaji bulan Maret tidak terbaca, mohon upload ulang' : 'Catatan bersifat opsional untuk status valid.')">
                                </p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">Proses
                                    Verifikasi</button>
                                <a href="{{ route('admin.dokumen.index') }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Batal</a>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>
@endsection
