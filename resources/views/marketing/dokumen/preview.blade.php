@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Preview Dokumen</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $document->nama_file }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('marketing.dokumen.download', $document->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">Download</a>
                <a href="{{ route('marketing.dokumen.index', $document->id_konsumen) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Tutup</a>
            </div>
        </div>

        <x-card>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jenis Dokumen</p>
                    <p class="mt-1 font-semibold text-gray-900">
                        {{ ucfirst(str_replace('_', ' ', $document->jenis_dokumen)) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status Verifikasi</p>
                    <p class="mt-1 font-semibold text-gray-900">
                        {{ ucfirst(str_replace('_', ' ', $document->status_verifikasi)) }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            @php $extension = strtolower(pathinfo($document->nama_file, PATHINFO_EXTENSION)); @endphp
            @if (in_array($extension, ['jpg', 'jpeg', 'png']))
                <img src="{{ asset('storage/' . $document->path_file) }}" alt="Preview Dokumen"
                    class="mx-auto max-h-[70vh] rounded-xl border border-gray-200 object-contain" />
            @else
                <iframe src="{{ asset('storage/' . $document->path_file) }}"
                    class="h-[70vh] w-full rounded-xl border border-gray-200"></iframe>
            @endif
        </x-card>
    </div>
@endsection
