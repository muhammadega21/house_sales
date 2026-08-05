@extends('layouts.app')

@section('title', 'Perbandingan Simulasi Pembayaran')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Perbandingan Simulasi Pembayaran</h1>
                <p class="text-sm text-gray-500">Lihat perbandingan total pembayaran untuk ketiga metode pembayaran.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('marketing.simulasi.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Kembali ke Simulasi
                </a>
                <a href="{{ route('marketing.simulasi.export-pdf', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700">
                    Export PDF
                </a>
            </div>
        </div>

        <x-card>
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Unit</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-700">
                        <p><span class="font-semibold text-slate-900">Kode Unit:</span> {{ $unit->kode_unit }}</p>
                        <p><span class="font-semibold text-slate-900">Tipe Rumah:</span> {{ $unit->tipe_rumah }}</p>
                        <p><span class="font-semibold text-slate-900">Kategori:</span> {{ $unit->kategori->value }}</p>
                        <p><span class="font-semibold text-slate-900">Harga Jual:</span> Rp
                            {{ number_format($unit->harga_jual, 0, ',', '.') }}</p>
                        <p><span class="font-semibold text-slate-900">Perumahan:</span>
                            {{ $unit->perumahan->nama_perumahan ?? '-' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Simulasi</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-700">
                        <p><span class="font-semibold text-slate-900">Konsumen:</span>
                            {{ $konsumen?->nama_lengkap ?? 'Tidak dipilih' }}</p>
                        <p><span class="font-semibold text-slate-900">DP Persen:</span>
                            {{ $hasilKpr['dp_persen'] ?? ($hasilCashBertahap['dp_persen'] ?? 0) }}%</p>
                        <p><span class="font-semibold text-slate-900">Tenor (tahun):</span>
                            {{ $hasilKpr['tenor_tahun'] ?? ($hasilCashBertahap['tenor_tahun'] ?? 0) }}</p>
                        <p><span class="font-semibold text-slate-900">Suku Bunga:</span>
                            {{ $hasilKpr['suku_bunga'] ?? 0 }}%</p>
                        <p><span class="font-semibold text-slate-900">Diskon Cash Keras:</span>
                            {{ $hasilCashKeras['diskon_persen'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Ringkasan Perbandingan</h2>
                    <p class="text-sm text-gray-500">Bandingkan total biaya dan cicilan untuk setiap metode pembayaran.</p>
                </div>
            </div>

            <div class="mt-6">
                <x-perbandingan-metode :hasil-kpr="$hasilKpr" :hasil-cash-bertahap="$hasilCashBertahap" :hasil-cash-keras="$hasilCashKeras" />
            </div>
        </x-card>
    </div>
@endsection
