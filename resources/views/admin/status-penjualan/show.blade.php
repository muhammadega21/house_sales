@extends('layouts.app')

@section('title', 'Detail Status Penjualan - ' . ($statusPenjualan->booking?->kode_booking ?? '-'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Status Penjualan</h1>
                <p class="mt-2 text-sm text-gray-600">{{ $statusPenjualan->booking?->kode_booking ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.status-penjualan.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Informasi Booking">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Kode Booking</p>
                            <p class="mt-2 text-sm text-gray-900">{{ $statusPenjualan->booking?->kode_booking ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Konsumen</p>
                            <p class="mt-2 text-sm text-gray-900">
                                {{ $statusPenjualan->booking?->konsumen?->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Unit</p>
                            <p class="mt-2 text-sm text-gray-900">{{ $statusPenjualan->unit?->kode_unit ?? '-' }} -
                                {{ $statusPenjualan->unit?->tipe_rumah ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Marketing</p>
                            <p class="mt-2 text-sm text-gray-900">
                                {{ $statusPenjualan->booking?->marketing?->nama_lengkap ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card title="Status Saat Ini">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <x-badge :status="$statusPenjualan->status_saat_ini" />
                                <span
                                    class="text-sm text-gray-500">{{ $statusPenjualan->tanggal_perubahan?->format('d M Y, H:i') ?? '-' }}</span>
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                {{ $statusPenjualan->status_saat_ini->description() }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                            <p class="font-semibold">Catatan Terakhir</p>
                            <p class="mt-1">{{ $statusPenjualan->catatan ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card title="Timeline Status">
                    <x-status-timeline :histories="$timeline" :current-status="$statusPenjualan->status_saat_ini" />
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Ubah Status">
                    @if ($statusPenjualan->status_saat_ini->value === 'serah_terima' || $statusPenjualan->status_saat_ini->value === 'batal')
                        <p class="text-sm text-gray-500">Status ini bersifat final dan tidak dapat diubah lagi.</p>
                    @else
                        <form action="{{ route('admin.status-penjualan.update', $statusPenjualan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status Baru</label>
                                    <select name="status_baru"
                                        class="mt-1 p-3 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                        required>
                                        <option value="">-- Pilih Status --</option>
                                        @foreach ($availableTransitions as $transition)
                                            <option value="{{ $transition->value }}"
                                                {{ old('status_baru') === $transition->value ? 'selected' : '' }}>
                                                {{ $transition->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                    <textarea name="catatan" rows="4"
                                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>{{ old('catatan') }}</textarea>
                                </div>

                                <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                                    <p class="font-semibold">Perhatian</p>
                                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-amber-800">
                                        @foreach ($availableTransitions as $transition)
                                            @if ($transition === \App\Enums\StatusPenjualan::Akad)
                                                <li>Jika berubah ke Akad, unit akan ditandai 'dijual' dan komisi akan
                                                    dihitung.</li>
                                            @endif
                                            @if ($transition === \App\Enums\StatusPenjualan::Batal)
                                                <li>Jika berubah ke Batal, unit akan kembali 'tersedia'.</li>
                                            @endif
                                            @if ($transition === \App\Enums\StatusPenjualan::SerahTerima)
                                                <li>Jika berubah ke Serah Terima, proses menjadi final dan tidak dapat
                                                    diubah lagi.</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <button type="submit"
                                    class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">Proses
                                    Perubahan</button>
                            </div>
                        </form>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
@endsection
