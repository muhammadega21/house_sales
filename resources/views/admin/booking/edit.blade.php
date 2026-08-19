@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Booking</h1>
                <p class="text-sm text-gray-500">Kode: {{ $booking->kode_booking }}</p>
            </div>
            <a href="{{ route('admin.booking.show', $booking->id) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>

        @if ($booking->statusPenjualan !== 'booking')
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-amber-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm font-semibold text-amber-800">Booking hanya bisa diedit jika status penjualan masih
                        'booking'. Status saat ini</p>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card title="Detail Booking">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="id_konsumen" class="block text-sm font-semibold text-gray-700 mb-1">Konsumen <span
                                class="text-red-500">*</span></label>
                        <select name="id_konsumen" id="id_konsumen"
                            {{ $booking->statusPenjualan !== 'booking' ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary {{ $booking->statusPenjualan !== 'booking' ? 'bg-gray-50' : '' }}">
                            <option value="">-- Pilih Konsumen --</option>
                            @foreach ($konsumenOptions as $id => $label)
                                <option value="{{ $id }}"
                                    {{ old('id_konsumen', $booking->id_konsumen) == $id ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('id_konsumen')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_unit" class="block text-sm font-semibold text-gray-700 mb-1">Unit Rumah <span
                                class="text-red-500">*</span></label>
                        <select name="id_unit" id="id_unit"
                            {{ $booking->statusPenjualan !== 'booking' ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary {{ $booking->statusPenjualan !== 'booking' ? 'bg-gray-50' : '' }}">
                            <option value="">-- Pilih Unit --</option>
                            @foreach ($unitOptions as $id => $label)
                                <option value="{{ $id }}"
                                    {{ old('id_unit', $booking->id_unit) == $id ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_unit')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_booking" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal
                            Booking</label>
                        <input type="date" name="tanggal_booking" id="tanggal_booking"
                            value="{{ old('tanggal_booking', $booking->tanggal_booking->format('Y-m-d')) }}"
                            {{ $booking->statusPenjualan !== 'booking' ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary {{ $booking->statusPenjualan !== 'booking' ? 'bg-gray-50' : '' }}">
                        @error('tanggal_booking')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="booking_fee" class="block text-sm font-semibold text-gray-700 mb-1">Booking Fee
                            (Rp)</label>
                        <input type="number" name="booking_fee" id="booking_fee"
                            value="{{ old('booking_fee', $booking->booking_fee) }}" min="0" step="1000"
                            {{ $booking->statusPenjualan !== 'booking' ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary {{ $booking->statusPenjualan !== 'booking' ? 'bg-gray-50' : '' }}">
                        @error('booking_fee')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="metode_bayar_fee" class="block text-sm font-semibold text-gray-700 mb-1">Metode
                            Bayar</label>
                        <select name="metode_bayar_fee" id="metode_bayar_fee"
                            {{ $booking->statusPenjualan !== 'booking' ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary {{ $booking->statusPenjualan !== 'booking' ? 'bg-gray-50' : '' }}">
                            <option value="">-- Pilih --</option>
                            @foreach (\App\Enums\MetodeBayar::cases() as $metode)
                                @if ($metode !== \App\Enums\MetodeBayar::Kpr)
                                    <option value="{{ $metode->value }}"
                                        {{ old('metode_bayar_fee', $booking->metode_bayar_fee) === $metode->value ? 'selected' : '' }}>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti Bayar Saat Ini</label>
                        @if ($booking->bukti_bayar_fee)
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-gray-600">{{ basename($booking->bukti_bayar_fee) }}</span>
                                <a href="{{ asset('storage/' . $booking->bukti_bayar_fee) }}" target="_blank"
                                    class="text-sm text-primary hover:text-primary-dark">Lihat</a>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Tidak ada bukti bayar</p>
                        @endif
                    </div>

                    <div>
                        <label for="bukti_bayar_fee" class="block text-sm font-semibold text-gray-700 mb-1">Ganti Bukti
                            Bayar</label>
                        <x-form-file name="bukti_bayar_fee" label="Upload Bukti Bayar (opsional)"
                            accept="image/jpeg,image/png,image/webp,application/pdf" />
                        @error('bukti_bayar_fee')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                        <x-form-textarea name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"
                            :value="old('catatan', $booking->catatan)" />
                        @error('catatan')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('admin.booking.show', $booking->id) }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" {{ $booking->statusPenjualan !== 'booking' ? 'disabled' : '' }}
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
