@extends('layouts.app')

@section('title', 'Ubah Status Pengajuan KPR')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Ubah Status Pengajuan KPR</h1>
                <p class="mt-1 text-sm text-gray-500">Kode #{{ str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm text-gray-500">Konsumen: {{ $pengajuan->konsumen?->nama_lengkap ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.pengajuan-kpr.show', $pengajuan->id) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <x-card>
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="list-disc pl-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pengajuan-kpr.proses-update-status', $pengajuan->id) }}">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="status_sebelum" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Saat Ini
                        </label>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                            <span class="font-semibold text-gray-900">
                                {{ $statusLabels[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label for="status_pengajuan" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Baru
                        </label>
                        <select name="status_pengajuan" id="status_pengajuan"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary"
                            required>
                            @foreach ($allowedStatus as $status)
                                <option value="{{ $status }}"
                                    {{ old('status_pengajuan') === $status ? 'selected' : '' }}>
                                    {{ $statusLabels[$status] ?? $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_pengajuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:ring-primary"
                        placeholder="Masukkan catatan terkait perubahan status..." required>{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.pengajuan-kpr.show', $pengajuan->id) }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                        Simpan
                    </button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
