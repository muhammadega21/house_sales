@extends('layouts.app')

@section('title', 'Input Pembayaran')

@section('content')
    <div class="space-y-6" x-data="pembayaranForm()">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Input Pembayaran</h1>
                <p class="text-sm text-gray-500">Input pembayaran untuk booking properti milik Anda.</p>
            </div>
            <a href="{{ route('marketing.pembayaran.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <form action="{{ route('marketing.pembayaran.store') }}" method="POST" enctype="multipart/form-data"
            id="pembayaranForm">
            @csrf

            <!-- Section 1: Pilih Booking -->
            <x-card title="Pilih Booking" subtitle="Pilih booking aktif yang Anda miliki">
                <div class="grid gap-4">
                    <div>
                        <label for="id_booking" class="block text-sm font-semibold text-gray-700 mb-1">
                            Booking <span class="text-red-500">*</span>
                        </label>
                        <select name="id_booking" id="id_booking"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                            x-on:change="fetchBooking($event.target.value)">
                            <option value="">-- Pilih Booking --</option>
                            @foreach ($bookingOptions as $id => $label)
                                <option value="{{ $id }}" {{ old('id_booking') == $id ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('id_booking')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <template x-if="loading">
                        <div class="mt-4 text-sm text-gray-500">Memuat data booking...</div>
                    </template>

                    <template x-if="bookingInfo">
                        <div class="mt-4 rounded-lg bg-gray-50 p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Info Booking Terpilih</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Kode Booking</p>
                                    <p class="font-semibold text-gray-900" x-text="bookingInfo.kode_booking"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Konsumen</p>
                                    <p class="font-semibold text-gray-900" x-text="bookingInfo.konsumen"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Unit</p>
                                    <p class="font-semibold text-gray-900" x-text="bookingInfo.unit"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Harga Unit</p>
                                    <p class="font-semibold text-gray-900" x-text="bookingInfo.harga_unit_format"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Sudah Dibayar (Verifikasi)</p>
                                    <p class="font-semibold text-gray-900" x-text="bookingInfo.total_terverifikasi_format">
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Sisa Tagihan</p>
                                    <p class="font-semibold text-gray-900 text-primary"
                                        x-text="bookingInfo.sisa_tagihan_format"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="errorMessage">
                        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700"
                            x-text="errorMessage"></div>
                    </template>
                </div>
            </x-card>

            <!-- Section 2: Detail Pembayaran -->
            <template x-if="bookingInfo && bookingInfo.sisa_tagihan > 0">
                <x-card title="Detail Pembayaran" subtitle="Lengkapi detail pembayaran">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="jenis_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">
                                Jenis Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_pembayaran" id="jenis_pembayaran"
                                class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                x-on:change="updateNominalSuggestion()" required>
                                <option value="">-- Pilih --</option>
                                @foreach (\App\Enums\JenisPembayaran::cases() as $jenis)
                                    <option value="{{ $jenis->value }}"
                                        {{ old('jenis_pembayaran') === $jenis->value ? 'selected' : '' }}>
                                        {{ $jenis->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_pembayaran')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nominal" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nominal (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="nominal" id="nominal" min="1" step="any"
                                value="{{ old('nominal') }}"
                                class="w-full rounded-lg border border-gray-300 py-2.5 pr-3 pl-12 text-sm transition focus:border-primary focus:ring-primary"
                                placeholder="0" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Saran: <span class="font-semibold" x-text="nominalSuggestion || '-'"></span>
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                DP minimal sesuai persentase unit. Cicilan otomatis dibagi 12 bulan.
                            </p>
                            @error('nominal')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_bayar" class="block text-sm font-semibold text-gray-700 mb-1">
                                Tanggal Bayar <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_bayar" id="tanggal_bayar"
                                value="{{ old('tanggal_bayar', now()->toDateString()) }}"
                                class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                required>
                            @error('tanggal_bayar')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ showRek: false }">
                            <label for="metode_bayar" class="block text-sm font-semibold text-gray-700 mb-1">
                                Metode Bayar <span class="text-red-500">*</span>
                            </label>
                            <select name="metode_bayar" id="metode_bayar"
                                class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                x-on:change="showRek = $el.value === 'transfer'" required>
                                <option value="">-- Pilih --</option>
                                @foreach (\App\Enums\MetodeBayar::cases() as $metode)
                                    <option value="{{ $metode->value }}"
                                        {{ old('metode_bayar') === $metode->value ? 'selected' : '' }}>
                                        {{ $metode->icon() }} {{ $metode->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('metode_bayar')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror

                            <template x-if="showRek">
                                <div class="mt-3">
                                    <label for="no_referensi" class="block text-sm font-semibold text-gray-700 mb-1">
                                        No. Referensi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="no_referensi" id="no_referensi"
                                        value="{{ old('no_referensi') }}"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                        placeholder="Masukkan no. referensi transfer">
                                    @error('no_referensi')
                                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </template>
                        </div>
                    </div>
                </x-card>
            </template>

            <template x-if="bookingInfo && bookingInfo.sisa_tagihan > 0">
                <x-card title="Bukti Pembayaran" subtitle="Upload bukti transfer/pembayaran">
                    <x-form-file name="bukti_bayar" label="Bukti Bayar"
                        accept="image/jpeg,image/png,image/webp,application/pdf" :required="true" />
                    <p class="mt-1 text-xs text-gray-400">
                        Format: JPG, PNG, PDF. Maksimal 5MB. Pastikan bukti jelas terbaca.
                    </p>
                </x-card>
            </template>

            <template x-if="bookingInfo && bookingInfo.sisa_tagihan === 0">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                    <div class="flex gap-3">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-800">Semua Tagihan Sudah Lunas</p>
                            <p class="mt-1 text-sm text-emerald-700">Booking ini telah melunasi seluruh sisa tagihan.
                                Form input pembayaran dinonaktifkan.</p>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="bookingInfo && bookingInfo.sisa_tagihan > 0">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                    <a href="{{ route('marketing.pembayaran.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" id="btnSimpan"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pembayaran
                    </button>
                </div>
            </template>
        </form>
    </div>

    @push('scripts')
        <script>
            function pembayaranForm() {
                return {
                    bookingInfo: null,
                    loading: false,
                    errorMessage: '',
                    nominalSuggestion: '',
                    init() {
                        const oldBooking = {{ json_encode(old('id_booking')) }};
                        if (oldBooking) {
                            this.fetchBooking(oldBooking);
                        }
                    },
                    async fetchBooking(idBooking) {
                        this.errorMessage = '';
                        this.bookingInfo = null;
                        this.nominalSuggestion = '';
                        if (!idBooking) return;

                        this.loading = true;
                        try {
                            const res = await fetch(`/marketing/pembayaran/info-booking/${idBooking}`);
                            const data = await res.json();
                            if (data.error) {
                                this.errorMessage = data.error;
                            } else {
                                this.bookingInfo = data;
                                this.$nextTick(() => {
                                    const jenis = document.getElementById('jenis_pembayaran')?.value;
                                    if (jenis) {
                                        this.updateNominalSuggestion();
                                    }
                                });
                            }
                        } catch (e) {
                            this.bookingInfo = null;
                            this.errorMessage = 'Gagal memuat data booking.';
                        }
                        this.loading = false;
                    },
                    updateNominalSuggestion() {
                        const jenis = document.getElementById('jenis_pembayaran')?.value;
                        if (!jenis || !this.bookingInfo) {
                            this.nominalSuggestion = '';
                            return;
                        }

                        let val = 0;
                        if (jenis === 'booking_fee') val = this.bookingInfo.booking_fee || 0;
                        else if (jenis === 'dp') val = this.bookingInfo.dp_minimum_nominal || 0;
                        else if (jenis === 'cicilan') val = (this.bookingInfo.sisa_tagihan || 0) / 12;
                        else if (jenis === 'pelunasan') val = this.bookingInfo.sisa_tagihan || 0;

                        this.nominalSuggestion = val > 0 ? 'Rp ' + Math.round(val).toLocaleString('id-ID') : '';
                    }
                }
            }
        </script>
    @endpush
@endsection
