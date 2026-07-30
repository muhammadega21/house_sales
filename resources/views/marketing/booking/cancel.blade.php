@extends('layouts.app')

@section('title', 'Batalkan Booking')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Batalkan Booking</h1>
            <p class="text-sm text-gray-500">Konfirmasi pembatalan booking</p>
        </div>
        <a href="{{ route('marketing.booking.show', $booking->id) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <div class="rounded-lg bg-red-50 border border-red-200 p-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-red-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800">Peringatan</p>
                <p class="mt-1 text-sm text-red-700">Setelah dibatalkan, unit akan dikembalikan ke status tersedia dan booking fee mungkin tidak dapat dikembalikan.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Booking</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->kode_booking }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Konsumen</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->konsumen?->nama_lengkap ?? '-' }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unit</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $booking->unit?->kode_unit ?? '-' }} - {{ $booking->unit?->tipe_rumah ?? '' }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Booking Fee</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($booking->booking_fee, 0, ',', '.') }}</p>
        </x-card>
    </div>

    <form action="{{ route('marketing.booking.process-cancel', $booking->id) }}" method="POST">
        @csrf
        <x-card title="Alasan Pembatalan">
            <div class="grid gap-4">
                <div>
                    <label for="alasan" class="block text-sm font-semibold text-gray-700 mb-1">
                        Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan" id="alasan" rows="4"
                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                        placeholder="Jelaskan alasan pembatalan..."
                        required>{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                @if($booking->status_pembayaran_fee === 'sudah_bayar')
                    <div class="flex items-start gap-3">
                        <input type="checkbox" name="pahami_tidak_dikembalikan" id="pahami_tidak_dikembalikan"
                            class="h-4 w-4 border-gray-300 text-primary focus:ring-primary mt-1" required>
                        <label for="pahami_tidak_dikembalikan" class="text-sm text-gray-700">
                            Saya mengerti bahwa booking fee <strong>tidak dapat dikembalikan</strong> sesuai kebijakan yang berlaku.
                        </label>
                    </div>
                    @error('pahami_tidak_dikembalikan')
                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                @endif
            </div>
        </x-card>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
            <a href="{{ route('marketing.booking.show', $booking->id) }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Ya, Batalkan Booking
            </button>
        </div>
    </form>
</div>
@endsection