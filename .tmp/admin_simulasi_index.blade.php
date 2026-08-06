@extends('layouts.app')

@section('title', 'Simulasi Pembayaran Admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Simulasi Pembayaran</h1>
                <p class="text-sm text-gray-500">Hitung estimasi cicilan unit rumah sebagai admin.</p>
            </div>
        </div>

        <x-card>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                <div class="flex items-start gap-3">
                    <div class="rounded-full bg-blue-600 p-2 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-blue-900">Disclaimer</h2>
                        <p class="mt-1 text-sm text-blue-700">Simulasi ini bersifat estimasi dan tidak mengikat. Hasil akhir
                            dapat berbeda berdasarkan kebijakan bank, harga final, atau kondisi unit.</p>
                    </div>
                </div>
            </div>
        </x-card>

        <x-simulasi-form :units="$units" :hitung-url="route('admin.simulasi.hitung')" :show-save-button="false" :show-export-button="false" :show-comparison-button="false" />
    </div>
@endsection
