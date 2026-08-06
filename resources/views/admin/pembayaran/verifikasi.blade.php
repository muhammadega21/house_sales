@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
    <div class="space-y-6" x-data="verifikasiForm()">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran</h1>
                <p class="text-sm text-gray-500">Kode Booking:
                    <span class="font-mono font-semibold">{{ $pembayaran->booking?->kode_booking ?? '-' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pembayaran.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14l9-9 9 9" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Detail
                </a>
            </div>
        </div>

        <!-- Info Pembayaran -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-card>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Konsumen</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $pembayaran->booking?->konsumen?->nama_lengkap ?? '-' }}</p>
                <p class="text-sm text-gray-500">{{ $pembayaran->booking?->unit?->kode_unit ?? '' }}</p>
            </x-card>
            <x-card>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Marketing</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $pembayaran->booking?->marketing?->nama_lengkap ?? '-' }}</p>
            </x-card>
            <x-card>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nominal</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                </p>
            </x-card>
        </div>

        <!-- Detail & Bukti Bayar -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="space-y-6">
                <x-card title="Detail Pembayaran" subtitle="Informasi lengkap pembayaran">
                    <div class="grid gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jenis Pembayaran</span>
                            <x-badge :status="$pembayaran->jenis_pembayaran" />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tanggal Bayar</span>
                            <span
                                class="font-semibold text-gray-900">{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Metode Bayar</span>
                            <span class="font-semibold text-gray-900">
                                {{ $pembayaran->metode_bayar ? \App\Enums\MetodeBayar::tryFrom($pembayaran->metode_bayar)?->label() : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">No. Referensi</span>
                            <span class="font-semibold text-gray-900">{{ $pembayaran->no_referensi ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tanggal Input</span>
                            <span
                                class="font-semibold text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </x-card>

                <!-- Perbandingan Nominal (opsional) -->
                @if ($expectedNominal > 0 && $pembayaran->jenis_pembayaran !== \App\Enums\JenisPembayaran::Cicilan)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Perbandingan Nominal</h4>
                        <div class="grid gap-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Nominal yang diinput</span>
                                <span class="font-semibold text-gray-900">Rp
                                    {{ number_format($pembayaran->nominal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Nominal yang diharapkan</span>
                                <span class="font-semibold text-gray-900">
                                    @if ($pembayaran->jenis_pembayaran === \App\Enums\JenisPembayaran::Dp)
                                        Rp {{ number_format($dpMinNominal, 0, ',', '.') }} ({{ $dpMinPersen }}% dari
                                        harga unit)
                                    @elseif ($pembayaran->jenis_pembayaran === \App\Enums\JenisPembayaran::Pelunasan)
                                        Rp {{ number_format($expectedNominal, 0, ',', '.') }} (sisa tagihan)
                                    @else
                                        Rp {{ number_format($expectedNominal, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            @php
                                $diffPersentase =
                                    $expectedNominal > 0
                                        ? (abs((float) $pembayaran->nominal - $expectedNominal) / $expectedNominal) *
                                            100
                                        : 0;
                            @endphp
                            @if ($diffPersentase > 5)
                                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-800">
                                    <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.33-7.33l-.01.01M12 3a9 9 0 110 18 9 9 0 010-18z" />
                                    </svg>
                                    <span class="font-medium">Peringatan:</span> Nominal yang diinput berbeda >5% dari yang
                                    diharapkan.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Bukti Bayar -->
            @if ($pembayaran->bukti_bayar)
                @php
                    $extension = strtolower(pathinfo($pembayaran->bukti_bayar, PATHINFO_EXTENSION));
                    $buktiUrl = asset('storage/' . $pembayaran->bukti_bayar);
                    $isPdf = in_array($extension, ['pdf']);
                @endphp
                <div class="space-y-4">
                    <x-card title="Bukti Pembayaran">
                        @if ($isPdf)
                            <div
                                class="flex h-64 w-full items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h10M7 14h10" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">File PDF — klik di bawah untuk melihat</p>
                                </div>
                            </div>
                            <a href="{{ $buktiUrl }}" target="_blank"
                                class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6h20M10 6l5 5M10 6l5-5" />
                                </svg>
                                Buka PDF di Tab Baru
                            </a>
                        @else
                            <img src="{{ $buktiUrl }}" alt="Bukti Bayar"
                                class="h-auto w-full max-h-80 rounded-xl border border-gray-200 object-contain cursor-zoom-in">
                            <a href="{{ $buktiUrl }}" target="_blank"
                                class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6h20M10 6l5 5M10 6l5-5" />
                                </svg>
                                Buka di Tab Baru
                            </a>
                        @endif
                    </x-card>
                </div>
            @else
                <div
                    class="flex items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 h-64">
                    <p class="text-sm text-gray-500">Tidak ada bukti pembayaran yang diupload.</p>
                </div>
            @endif
        </div>

        <!-- Form Verifikasi -->
        <x-card title="Form Verifikasi" subtitle="Pilih aksi verifikasi untuk pembayaran ini">
            <form action="{{ route('admin.pembayaran.proses-verifikasi', $pembayaran->id) }}" method="POST"
                id="formVerifikasi">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Aksi Verifikasi <span
                                class="text-red-500">*</span></p>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status_verifikasi" value="diverifikasi"
                                    x-model="selectedStatus" required>
                                <span class="text-sm font-medium text-gray-700">Verifikasi (Setujui)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status_verifikasi" value="ditolak" x-model="selectedStatus"
                                    required>
                                <span class="text-sm font-medium text-gray-700">Tolak</span>
                            </label>
                        </div>
                        @error('status_verifikasi')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="selectedStatus === 'ditolak'" x-transition>
                        <label for="catatan_verifikasi" class="block text-sm font-semibold text-gray-700 mb-1">
                            Catatan / Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="catatan_verifikasi" id="catatan_verifikasi" rows="3"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                            placeholder="Masukkan alasan penolakan..." x-model="catatanDraft"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Wajib diisi jika menolak pembayaran ini.</p>
                        @error('catatan_verifikasi')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="selectedStatus === 'diverifikasi'" x-transition>
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800">
                            <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pembayaran akan ditandai sebagai <b>diverifikasi</b>. Status booking fee akan
                            otomatis diperbarui jika ini adalah pembayaran booking fee.
                        </div>
                    </div>
                </div>
            </form>
        </x-card>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('admin.pembayaran.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Batal
            </a>
            <button type="button" id="btnProsesVerifikasi"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Verifikasi
            </button>
        </div>
    </div>

    <!-- Konfirmasi Modal -->
    <div id="konfirmasiModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modalBackdrop"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Konfirmasi Verifikasi
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" id="modalMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="btnKonfirmasiVerifikasi"
                            class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark sm:ml-3 sm:w-auto">
                            Ya, Proses
                        </button>
                        <button type="button" id="btnBatalVerifikasi"
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
            function verifikasiForm() {
                return {
                    selectedStatus: 'diverifikasi',
                    catatanDraft: '',
                    init() {
                        const oldStatus = {{ json_encode(old('status_verifikasi')) }};
                        if (oldStatus) {
                            this.selectedStatus = oldStatus;
                        }
                        const oldCatatan = {{ json_encode(old('catatan_verifikasi')) }};
                        if (oldCatatan) {
                            this.catatanDraft = oldCatatan;
                        }
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('konfirmasiModal');
                const btnProses = document.getElementById('btnProsesVerifikasi');
                const btnKonfirmasi = document.getElementById('btnKonfirmasiVerifikasi');
                const btnBatal = document.getElementById('btnBatalVerifikasi');
                const form = document.getElementById('formVerifikasi');

                function openModal() {
                    const radios = document.querySelectorAll('input[name="status_verifikasi"]');
                    let status = 'diverifikasi';
                    radios.forEach(r => {
                        if (r.checked) status = r.value;
                    });
                    const catatan = document.getElementById('catatan_verifikasi')?.value || '';

                    if (status === 'ditolak' && !catatan.trim()) {
                        alert('Catatan / alasan penolakan wajib diisi.');
                        return;
                    }

                    const msg = status === 'diverifikasi' ?
                        'Anda akan memverifikasi pembayaran ini. Pastikan semua data sudah benar.' :
                        'Anda akan Menolak pembayaran ini. Pastikan alasan sudah benar.';
                    document.getElementById('modalMessage').textContent = msg;

                    modal.classList.remove('hidden');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                }

                btnProses?.addEventListener('click', openModal);
                btnKonfirmasi?.addEventListener('click', function() {
                    closeModal();
                    form.submit();
                });
                btnBatal?.addEventListener('click', closeModal);

                modal?.addEventListener('click', function(e) {
                    if (e.target === modal || e.target.id === 'modalBackdrop') {
                        closeModal();
                    }
                });
            });
        </script>
    @endpush
@endsection
