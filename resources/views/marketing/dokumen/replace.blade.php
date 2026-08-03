@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Perbarui Dokumen</h1>
                <p class="mt-1 text-sm text-gray-500">Upload ulang dokumen untuk revisi administrasi.</p>
            </div>
            <a href="{{ route('marketing.dokumen.index', $document->id_konsumen) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Batal</a>
        </div>

        <x-card>
            <div class="space-y-4">
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                    Setelah upload ulang, status dokumen kembali menjadi belum diverifikasi.
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm font-semibold text-gray-900">Dokumen saat ini</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $document->nama_file }} · {{ $document->status_verifikasi }}
                    </p>
                </div>
            </div>
        </x-card>

        <form action="{{ route('marketing.dokumen.update', $document->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')
            <x-card>
                <div>
                    <label for="file_dokumen" class="mb-1.5 block text-sm font-semibold text-gray-700">Upload File Baru
                        <span class="text-red-500">*</span></label>
                    <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm" />
                    @error('file_dokumen')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-card>

            <div class="flex justify-end gap-3">
                <a href="{{ route('marketing.dokumen.index', $document->id_konsumen) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Batal</a>
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">Upload
                    Ulang</button>
            </div>
        </form>
    </div>
@endsection
