@extends('layouts.app')

@section('title', 'Buat Booking Baru')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Booking Baru</h1>
                <p class="text-sm text-gray-500">Pesan unit rumah untuk konsumen Anda.</p>
            </div>
            <a href="{{ route('marketing.booking.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <form action="{{ route('marketing.booking.store') }}" method="POST" enctype="multipart/form-data" id="bookingForm">
            @csrf

            <!-- Section 1: Pilih Konsumen -->
            <x-card title="Pilih Konsumen" subtitle="Pilih konsumen yang sudah terdaftar">
                <div class="grid gap-4">
                    <div>
                        <label for="id_konsumen" class="block text-sm font-semibold text-gray-700 mb-1">
                            Konsumen <span class="text-red-500">*</span>
                        </label>
                        <select name="id_konsumen" id="id_konsumen"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">-- Pilih Konsumen --</option>
                            @foreach ($konsumenOptions as $id => $label)
                                <option value="{{ $id }}" {{ old('id_konsumen') == $id ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('id_konsumen')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    @if (old('id_konsumen'))
                        @php
                            $selectedKonsumen = \App\Models\Konsumen::find(old('id_konsumen'));
                        @endphp
                        @if ($selectedKonsumen)
                            <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Info Konsumen Terpilih</h4>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Nama</p>
                                        <p class="font-semibold text-gray-900">{{ $selectedKonsumen->nama_lengkap }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">NIK</p>
                                        <p class="font-semibold text-gray-900">{{ $selectedKonsumen->nik }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">No HP</p>
                                        <p class="font-semibold text-gray-900">{{ $selectedKonsumen->no_hp }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </x-card>

            <!-- Section 2: Pilih Unit Rumah -->
            <x-card title="Pilih Unit Rumah" subtitle="Pilih unit yang tersedia (status: tersedia)">
                <div class="grid gap-4"
                    x-data="{
                        unitInfo: null,
                        loading: false,
                        errorMessage: '',
                        async cekUnit(idUnit) {
                            this.errorMessage = '';
                            this.unitInfo = null;
                            if (!idUnit) return;

                            this.loading = true;
                            try {
                                const res = await fetch(`/marketing/booking/cek-unit/${idUnit}`);
                                const data = await res.json();
                                this.unitInfo = data;
                                if (!data.available) {
                                    this.errorMessage = data.message || 'Unit tidak tersedia.';
                                }
                            } catch (e) {
                                this.unitInfo = null;
                                this.errorMessage = 'Gagal memuat data unit.';
                            }
                            this.loading = false;
                        }
                    }">
                    <div>
                        <label for="id_unit" class="block text-sm font-semibold text-gray-700 mb-1">
                            Unit Rumah <span class="text-red-500">*</span>
                        </label>
                        <select name="id_unit" id="id_unit" x-on:change="cekUnit($event.target.value)"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">-- Pilih Unit --</option>
                            @foreach ($unitOptions as $id => $label)
                                <option value="{{ $id }}" {{ old('id_unit') == $id ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('id_unit')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <template x-if="loading">
                        <div class="mt-4 text-sm text-gray-500">Memuat data unit...</div>
                    </template>

                    <template x-if="unitInfo && unitInfo.available">
                        <div class="mt-4 rounded-lg bg-gray-50 p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Info Unit Terpilih</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Kode Unit</p>
                                    <p class="font-semibold text-gray-900" x-text="unitInfo.kode_unit"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Tipe</p>
                                    <p class="font-semibold text-gray-900" x-text="unitInfo.tipe_rumah"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Kategori</p>
                                    <p class="font-semibold text-gray-900" x-text="unitInfo.kategori"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Luas (m²)</p>
                                    <p class="font-semibold text-gray-900" x-text="unitInfo.luas_tanah + ' x ' + unitInfo.luas_bangunan"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Harga Jual</p>
                                    <p class="font-semibold text-gray-900" x-text="unitInfo.harga_jual_format"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Booking Fee Min</p>
                                    <p class="font-semibold text-gray-900" x-text="'Rp ' + unitInfo.booking_fee_min.toLocaleString('id-ID')"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="errorMessage">
                        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700" x-text="errorMessage"></div>
                    </template>
                </div>
            </x-card>

            <!-- Section 3: Detail Booking -->
            <x-card title="Detail Booking">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="tanggal_booking" class="block text-sm font-semibold text-gray-700 mb-1">
                            Tanggal Booking <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_booking" id="tanggal_booking"
                            value="{{ old('tanggal_booking', now()->toDateString()) }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @error('tanggal_booking')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="booking_fee" class="block text-sm font-semibold text-gray-700 mb-1">
                            Booking Fee (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="booking_fee" id="booking_fee" value="{{ old('booking_fee') }}"
                            min="0" step="1000"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                            placeholder="Minimal Rp 1.000.000 (subsidi) atau Rp 5.000.000 (non-subsidi)">
                        @error('booking_fee')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="metode_bayar_fee" class="block text-sm font-semibold text-gray-700 mb-1">Metode
                            Bayar</label>
                        <select name="metode_bayar_fee" id="metode_bayar_fee"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">-- Pilih --</option>
                            @foreach (\App\Enums\MetodeBayar::cases() as $metode)
                                @if ($metode !== \App\Enums\MetodeBayar::Kpr)
                                    <option value="{{ $metode->value }}"
                                        {{ old('metode_bayar_fee') === $metode->value ? 'selected' : '' }}>
                                        {{ $metode->icon() }} {{ $metode->label() }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('metode_bayar_fee')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bukti_bayar_fee" class="block text-sm font-semibold text-gray-700 mb-1">Bukti
                            Bayar</label>
                        <x-form-file name="bukti_bayar_fee" label="Upload Bukti Bayar"
                            accept="image/jpeg,image/png,image/webp,application/pdf" />
                        @error('bukti_bayar_fee')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                        <x-form-textarea name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"
                            :value="old('catatan')" />
                        @error('catatan')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <!-- Info Box -->
            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">Informasi Booking</p>
                        <p class="mt-1 text-sm text-blue-700">Unit akan dikunci (status: di-booking) setelah booking
                            disimpan. Booking hangus jika tidak ada pembayaran DP dalam 14 hari.</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('marketing.booking.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="button" id="btnKonfirmasi"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Booking
                </button>
            </div>
        </form>
    </div>

    <!-- Konfirmasi Modal -->
    <div id="konfirmasiModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modalBackdrop"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Konfirmasi Booking</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" id="modalMessage">
                                        Anda akan mengbooking unit ini. Unit akan dikunci dan tidak bisa dibooking orang lain. Lanjutkan?
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="btnYa"
                            class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark sm:ml-3 sm:w-auto">Ya,
                            Lanjutkan</button>
                        <button type="button" id="btnBatal"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('konfirmasiModal');
        const btnKonfirmasi = document.getElementById('btnKonfirmasi');
        const btnYa = document.getElementById('btnYa');
        const btnBatal = document.getElementById('btnBatal');
        const form = document.getElementById('bookingForm');

        function openModal() {
            modal.classList.remove('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        btnKonfirmasi.addEventListener('click', openModal);
        btnBatal.addEventListener('click', closeModal);
        
        btnYa.addEventListener('click', function() {
            closeModal();
            form.submit();
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.id === 'modalBackdrop') {
                closeModal();
            }
        });
    });
</script>
@endpush
