@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Upload Dokumen KPR</h1>
                <p class="mt-1 text-sm text-gray-500">Upload dokumen persyaratan untuk {{ $konsumen->nama_lengkap }}</p>
            </div>
            <a href="{{ route('marketing.dokumen.index', $konsumen->id) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Batal</a>
        </div>

        <x-card>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Konsumen</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $konsumen->nama_lengkap }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">NIK</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $konsumen->nik }}</p>
                </div>
            </div>
        </x-card>

        <form action="{{ route('marketing.dokumen.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
            x-data="{ isDragging: false, fileName: '', error: '', maxSize: 5120, selectedType: '' }"
            @submit.prevent="const fileInput = $refs.fileInput; const file = fileInput.files[0]; if (!file) { this.error = 'Pilih file dokumen terlebih dahulu.'; return; } const ext = file.name.split('.').pop()?.toLowerCase(); const allowed = ['pdf','jpg','jpeg','png']; if (!allowed.includes(ext)) { this.error = 'Format file tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG.'; return; } if (file.size > this.maxSize * 1024 * 1024) { this.error = `Ukuran file melebihi batas ${this.maxSize}MB.`; return; } this.error = ''; $el.submit();">
            @csrf
            <input type="hidden" name="id_konsumen" value="{{ $konsumen->id }}">

            <x-card>
                <div class="space-y-4">
                    <div>
                        <label for="jenis_dokumen" class="mb-1.5 block text-sm font-semibold text-gray-700">Jenis Dokumen
                            <span class="text-red-500">*</span></label>
                        <select name="jenis_dokumen" id="jenis_dokumen"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            x-model="selectedType"
                            x-on:change="const selected = $event.target.selectedOptions[0]; const size = Number(selected.dataset.maxSize || 5120); this.maxSize = size; document.getElementById('fileHelp').textContent = selected.dataset.help || 'Format PDF, JPG, JPEG, PNG';">
                            <option value="">Pilih jenis dokumen</option>
                            @foreach ($dokumenOptions as $option)
                                <option value="{{ $option['value'] }}" data-max-size="{{ $option['wajib'] ? 5120 : 10240 }}"
                                    data-help="{{ $option['keterangan'] }}" {{ $option['disabled'] ? 'disabled' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_dokumen')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="file_dokumen" class="mb-1.5 block text-sm font-semibold text-gray-700">Upload File <span
                                class="text-red-500">*</span></label>
                        <div data-max-size="5120"
                            class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 text-center transition hover:border-primary"
                            @dragover.prevent="isDragging=true" @dragleave.prevent="isDragging=false" @drop.prevent="">
                            <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0]?.name || '';" />
                            <label for="file_dokumen"
                                class="flex cursor-pointer flex-col items-center justify-center gap-2">
                                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-sm font-semibold text-primary">Klik untuk upload atau seret file</p>
                                <p class="text-xs text-gray-500" id="fileHelp">Format PDF, JPG, JPEG, PNG. Maksimal 5MB
                                    untuk gambar / 10MB untuk PDF.</p>
                                <p class="text-xs font-medium text-gray-600" x-text="fileName || 'Belum ada file dipilih'">
                                </p>
                                <template x-if="error">
                                    <p class="text-xs font-semibold text-red-600" x-text="error"></p>
                                </template>
                            </label>
                        </div>
                        @error('file_dokumen')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-700">
                    Dokumen akan diverifikasi oleh Admin. Pastikan dokumen jelas dan terbaca.
                </div>
            </x-card>

            <div class="flex justify-end gap-3">
                <a href="{{ route('marketing.dokumen.index', $konsumen->id) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Batal</a>
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">Upload</button>
            </div>
        </form>
    </div>
@endsection
