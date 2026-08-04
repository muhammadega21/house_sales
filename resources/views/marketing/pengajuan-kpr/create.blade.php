@extends('layouts.app')

@section('title', 'Buat Pengajuan KPR Baru')

@section('content')
    <div class="space-y-6" x-data="pengajuanKprForm()">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Pengajuan KPR Baru</h1>
                <p class="text-sm text-gray-500">Ajukan KPR untuk booking konsumen Anda.</p>
            </div>
            <a href="{{ route('marketing.pengajuan-kpr.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali ke Daftar
            </a>
        </div>

        <form action="{{ route('marketing.pengajuan-kpr.store') }}" method="POST" id="pengajuanKprForm">
            @csrf

            <x-card title="Pilih Booking" subtitle="Pilih booking yang akan diajukan KPR">
                <div class="grid gap-4">
                    <div>
                        <label for="id_booking" class="block text-sm font-semibold text-gray-700 mb-1">
                            Booking <span class="text-red-500">*</span>
                        </label>
                        <select name="id_booking" id="id_booking" x-on:change="fetchBooking($event.target.value)"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                            <option value="">-- Pilih Booking --</option>
                            @foreach ($bookingOptions as $id => $label)
                                <option value="{{ $id }}" {{ old('id_booking') == $id ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('id_booking')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <template x-if="bookingInfo">
                        <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Info Booking</h4>
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
                            </div>
                        </div>
                    </template>

                    <template x-if="errorMessage">
                        <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700"
                            x-text="errorMessage"></div>
                    </template>
                </div>
            </x-card>

            <x-card title="Detail Pengajuan KPR" subtitle="Isi data pengajuan KPR konsumen">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="nama_bank" class="block text-sm font-semibold text-gray-700 mb-1">Nama Bank <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_bank" id="nama_bank" value="{{ old('nama_bank') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @error('nama_bank')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="plafon_kpr" class="block text-sm font-semibold text-gray-700 mb-1">Plafon KPR (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="plafon_kpr" id="plafon_kpr" min="0" step="1000" value="{{ old('plafon_kpr') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @error('plafon_kpr')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tenor_tahun" class="block text-sm font-semibold text-gray-700 mb-1">Tenor (tahun) <span class="text-red-500">*</span></label>
                        <input type="number" name="tenor_tahun" id="tenor_tahun" min="1" max="30" value="{{ old('tenor_tahun') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @error('tenor_tahun')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="suku_bunga" class="block text-sm font-semibold text-gray-700 mb-1">Suku Bunga (%)</label>
                        <input type="number" name="suku_bunga" id="suku_bunga" min="0" step="0.01" value="{{ old('suku_bunga') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @error('suku_bunga')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_pengajuan" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" id="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', now()->toDateString()) }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @error('tanggal_pengajuan')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="4"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('marketing.pengajuan-kpr.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pengajuan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function pengajuanKprForm() {
                return {
                    bookingInfo: null,
                    errorMessage: '',
                    async fetchBooking(idBooking) {
                        this.bookingInfo = null;
                        this.errorMessage = '';

                        if (!idBooking) {
                            return;
                        }

                        try {
                            const response = await fetch(`/marketing/pengajuan-kpr/info-booking/${idBooking}`);
                            const data = await response.json();

                            if (!response.ok) {
                                this.errorMessage = data.error ?? 'Gagal memuat informasi booking.';
                                return;
                            }

                            if (data.error) {
                                this.errorMessage = data.error;
                                return;
                            }

                            this.bookingInfo = data;
                        } catch (error) {
                            this.errorMessage = 'Gagal memuat informasi booking.';
                        }
                    },
                }
            }
        </script>
    @endpush
@endsection
