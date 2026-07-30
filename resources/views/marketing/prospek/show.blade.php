@extends('layouts.app')

@section('title', 'Detail Prospek')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $prospek->nama_prospek }}</h1>
            <p class="text-sm text-gray-500">Detail prospek</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.prospek.edit', $prospek->id) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @if($prospek->status_prospek->value === 'berminat')
                <a href="{{ route('marketing.prospek.convert', $prospek->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                    Konversi
                </a>
            @endif
            <a href="{{ route('marketing.prospek.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $prospek->nama_prospek }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">No HP</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $prospek->no_hp }}</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $prospek->email ?? '-' }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sumber</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">
                @php $sumber = $prospek->sumber_prospek; @endphp
                {{ $sumber ? $sumber->icon() . ' ' . $sumber->label() : '-' }}
            </p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $prospek->status_prospek->color() }}-100 text-{{ $prospek->status_prospek->color() }}-800">
                    {{ $prospek->status_prospek->label() }}
                </span>
            </p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $prospek->tanggal_prospek->format('d/m/Y') }}</p>
        </x-card>
    </div>

    @if($prospek->catatan)
        <x-card>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Catatan</h2>
            <p class="text-gray-700">{{ $prospek->catatan }}</p>
        </x-card>
    @endif

    <x-card>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Marketing</h2>
        <p class="text-gray-700">{{ $prospek->marketing?->nama_lengkap ?? '-' }}</p>
    </x-card>
</div>
@endsection