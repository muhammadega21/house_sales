@extends('layouts.app')

@section('title', 'Tolak Pembayaran')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tolak Pembayaran</h1>
            <p class="text-sm text-gray-500">Kode Booking:
                <span class="font-mono font-semibold">{{ $pembayaran->booking?->kode_booking ?? '-' }}</span>
            </p>
        </div>
        <a href="{{ route('admin.pembayaran.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14l9-9 9 9" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Info Ringkas -->
    <x-card title="Detail Pembayaran">
        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <span class="text-gray-500">Konsumen</span>
                <span class="block font-semibold text-gray-900 mt-1">{{ $pembayaran->booking?->konsumen?->nama_lengkap ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Marketing</span>
                <span class="block font-semibold text-gray-900 mt-1">{{ $pembayaran->booking?->marketing?->nama_lengkap ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Jenis</span>
                <span class="block font-semibold text-gray-900 mt-1">
                    <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border bg-{{ $pembayaran->jenis_pembayaran->color() }}-100 text-{{ $pembayaran->jenis_pembayaran->color() }}-800">
                        {{ $pembayaran->jenis_pembayaran->label() }}
                    </span>
                </span>
            </div>
            <div>
                <span class="text-gray-500">Nominal</span>
                <span class="block font-semibold text-gray-900 mt-1">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Tanggal Bayar</span>
                <span class="block font-semibold text-gray-900 mt-1">{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Metode</span>
                <span class="block font-semibold text-gray-900 mt-1">{{ \App\Enums\MetodeBayar::tryFrom($pembayaran->metode_bayar)?->label() ?? '-' }}</span>
            </div>
        </div>
    </x-card>

    <!-- Form Tolak -->
    <x-card title="Alasan Penolakan" subtitle="Berikan alasan mengapa pembayaran ini ditolak">
        <form action="{{ route('admin.pembayaran.proses-tolak', $pembayaran->id) }}" method="POST" id="formTolak">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="catatan_verifikasi" class="block text-sm font-semibold text-gray-700 mb-1">
                        Catatan / Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="catatan_verifikasi" id="catatan_verifikasi" rows="4" required
                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition focus:border-danger focus:ring-danger"
                        placeholder="Misalnya: Bukti transfer tidak jelas, nominal tidak sesuai, dll."></textarea>
                    @error('catatan_verifikasi')
                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Alasan penolakan wajib diisi dan akan dikirimkan ke marketing.</p>
                </div>

                <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-red-900">Konfirmasi Penolakan</p>
                            <p class="mt-1 text-sm text-red-800">
                                Pembayaran ini akan ditolak dan tidak dapat dikembalikan.
                                Marketing akan menerima notifikasi beserta alasan penolakan ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.pembayaran.verifikasi', $pembayaran->id) }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            Batal
        </a>
        <button type="button" id="btnTolak"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tolak Pembayaran
        </button>
    </div>
</div>

<!-- Konfirmasi Modal -->
<div id="konfirmasiModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modalBackdrop"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Konfirmasi Penolakan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menolak pembayaran ini?
                                    Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" id="btnKonfirmasiTolak"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">
                        Ya, Tolak
                    </button>
                    <button type="button" id="btnBatalTolak"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('konfirmasiModal');
    const btnTolak = document.getElementById('btnTolak');
    const btnKonfirmasi = document.getElementById('btnKonfirmasiTolak');
    const btnBatal = document.getElementById('btnBatalTolak');
    const form = document.getElementById('formTolak');

    btnTolak?.addEventListener('click', function() {
        const catatan = document.getElementById('catatan_verifikasi')?.value;
        if (!catatan || !catatan.trim()) {
            alert('Catatan / alasan penolakan wajib diisi.');
            return;
        }
        modal.classList.remove('hidden');
    });

    btnKonfirmasi?.addEventListener('click', function() {
        modal.classList.add('hidden');
        form.submit();
    });

    btnBatal?.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    modal?.addEventListener('click', function(e) {
        if (e.target === modal || e.target.id === 'modalBackdrop') {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endpush
@endsection
