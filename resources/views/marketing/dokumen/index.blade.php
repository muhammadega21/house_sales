@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dokumen KPR</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola dokumen persyaratan KPR untuk {{ $konsumen->nama_lengkap }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('marketing.dokumen.create', $konsumen->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload Dokumen
                </a>
                <a href="{{ route('marketing.konsumen.show', $konsumen->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Kembali ke Konsumen
                </a>
            </div>
        </div>

        <x-card>
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Informasi Konsumen</p>
                    <p class="mt-1 text-sm text-gray-500">Nama: {{ $konsumen->nama_lengkap }} · NIK: {{ $konsumen->nik }} ·
                        No HP: {{ $konsumen->no_hp }}</p>
                </div>
                <div class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
                    {{ $isComplete ? 'Semua dokumen wajib valid' : 'Belum lengkap' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="font-semibold text-gray-700">Kelengkapan Dokumen Wajib</span>
                    <span class="text-gray-500">{{ count(array_filter($checklist, fn($item) => $item['is_valid'])) }} dari
                        {{ count($checklist) }} dokumen wajib valid</span>
                </div>
                <div class="h-2 rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-primary transition-all"
                        style="width: {{ count($checklist) > 0 ? (count(array_filter($checklist, fn($item) => $item['is_valid'])) / count($checklist)) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Checklist Kelengkapan Dokumen</h2>
                    <p class="text-sm text-gray-500">Dokumen wajib yang belum ada akan diberi highlight merah.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Jenis Dokumen</th>
                            <th class="px-4 py-3">Wajib/Opsional</th>
                            <th class="px-4 py-3">Status Upload</th>
                            <th class="px-4 py-3">Status Verifikasi</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($checklist as $item)
                            <tr class="@if (!$item['uploaded']) bg-red-50 @endif">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item['label'] }}</td>
                                <td class="px-4 py-3">{{ $item['wajib'] ? 'Wajib' : 'Opsional' }}</td>
                                <td class="px-4 py-3">
                                    @if ($item['uploaded'])
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Ada</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Belum
                                            Ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item['uploaded'])
                                        @php $status = $item['status_verifikasi'] ?? 'belum_diverifikasi'; @endphp
                                        <span
                                            class="inline-flex items-center rounded-full bg-{{ $status === 'valid' ? 'emerald' : ($status === 'perlu_revisi' ? 'amber' : 'gray') }}-100 px-2.5 py-1 text-xs font-semibold text-{{ $status === 'valid' ? 'emerald' : ($status === 'perlu_revisi' ? 'amber' : 'gray') }}-700">
                                            {{ $status === 'valid' ? 'Valid' : ($status === 'perlu_revisi' ? 'Perlu Revisi' : ($status === 'tidak_valid' ? 'Tidak Valid' : 'Belum Diverifikasi')) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item['uploaded'])
                                        <a href="{{ route('marketing.dokumen.preview', $item['document']->id) }}"
                                            class="text-sm font-semibold text-primary hover:text-primary-dark">Preview</a>
                                    @else
                                        <a href="{{ route('marketing.dokumen.create', $konsumen->id) }}"
                                            class="text-sm font-semibold text-primary hover:text-primary-dark">Upload</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Dokumen Terupload</h2>
                    <p class="text-sm text-gray-500">Kelola dokumen yang telah Anda upload untuk konsumen ini.</p>
                </div>
            </div>
            @if ($documents->isEmpty())
                <x-empty-state title="Belum ada dokumen diupload." message="Mulai upload dokumen persyaratan KPR."
                    create-route="{{ route('marketing.dokumen.create', $konsumen->id) }}" create-label="Upload Dokumen" />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Nama File</th>
                                <th class="px-4 py-3">Ukuran</th>
                                <th class="px-4 py-3">Tanggal Upload</th>
                                <th class="px-4 py-3">Status Verifikasi</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($documents as $index => $document)
                                <tr>
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3"><span
                                            class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ ucfirst(str_replace('_', ' ', $document->jenis_dokumen)) }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $document->nama_file }}</td>
                                    <td class="px-4 py-3">
                                        {{ $document->ukuran_file ? number_format($document->ukuran_file / 1024, 1) . ' KB' : '-' }}
                                    </td>
                                    <td class="px-4 py-3">{{ $document->tanggal_upload?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full bg-{{ $document->status_verifikasi === 'valid' ? 'emerald' : ($document->status_verifikasi === 'perlu_revisi' ? 'amber' : 'gray') }}-100 px-2.5 py-1 text-xs font-semibold text-{{ $document->status_verifikasi === 'valid' ? 'emerald' : ($document->status_verifikasi === 'perlu_revisi' ? 'amber' : 'gray') }}-700">
                                            {{ $document->status_verifikasi === 'valid' ? 'Valid' : ($document->status_verifikasi === 'perlu_revisi' ? 'Perlu Revisi' : ($document->status_verifikasi === 'tidak_valid' ? 'Tidak Valid' : 'Belum Diverifikasi')) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('marketing.dokumen.preview', $document->id) }}"
                                                class="text-sm font-semibold text-primary hover:text-primary-dark">Preview</a>
                                            <a href="{{ route('marketing.dokumen.download', $document->id) }}"
                                                class="text-sm font-semibold text-primary hover:text-primary-dark">Download</a>
                                            @if ($document->status_verifikasi === 'perlu_revisi')
                                                <a href="{{ route('marketing.dokumen.replace', $document->id) }}"
                                                    class="text-sm font-semibold text-amber-600 hover:text-amber-700">Ganti</a>
                                            @endif
                                            @if (in_array($document->status_verifikasi, ['belum_diverifikasi', 'perlu_revisi'], true))
                                                <form action="{{ route('marketing.dokumen.destroy', $document->id) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Hapus dokumen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-sm font-semibold text-red-600 hover:text-red-700">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

        <div class="flex items-center justify-end">
            <a href="{{ route('marketing.konsumen.show', $konsumen->id) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                @if (!$isComplete) disabled @endif
                title="{{ $isComplete ? '' : 'Lengkapi dokumen wajib terlebih dahulu' }}">
                Ajukan KPR
            </a>
        </div>
    </div>
@endsection
