@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Prospek</h1>
            <p class="mt-1 text-sm text-gray-500">Prospek #{{ $prospek->id }} — {{ $prospek->nama_prospek }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.prospek.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <a href="{{ route('admin.prospek.edit', $prospek->id) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a href="{{ route('admin.prospek.convert', $prospek->id) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                </svg>
                Konversi
            </a>
            @if($prospek->status_prospek->value !== 'jadi_konsumen')
                <x-confirm-delete :route="route('admin.prospek.destroy', $prospek->id)" :item-name="$prospek->nama_prospek" />
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Main Detail --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <div class="grid gap-6">
                    <div class="grid gap-x-6 md:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Prospek</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $prospek->nama_prospek }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor HP</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $prospek->no_hp }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $prospek->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber Prospek</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                @php $sumber = $prospek->sumber_prospek; @endphp
                                {{ $sumber ? $sumber->icon() . ' ' . $sumber->label() : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                            <p class="mt-1">
                                @php $status = $prospek->status_prospek; @endphp
                                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border {{ match($status->value) {
                                    'baru' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'dihubungi' => 'bg-sky-100 text-sky-800 border-sky-200',
                                    'berminat' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'tidak_berminat' => 'bg-red-100 text-red-800 border-red-200',
                                    'jadi_konsumen' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200',
                                } }}">
                                    {{ $status->label() }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Prospek</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $prospek->tanggal_prospek->format('d F Y') }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan / Komunikasi</p>
                        <p class="mt-1 text-sm text-gray-700 whitespace-pre-line bg-gray-50 rounded-lg p-3 border border-gray-200">{{ $prospek->catatan ?: '-' }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <x-card>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Marketing PIC</h3>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                        {{ strtoupper(mb_substr($prospek->marketing->nama_lengkap ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $prospek->marketing->nama_lengkap ?? 'Tidak diketahui' }}</p>
                        <p class="text-xs text-gray-500">{{ $prospek->marketing->username ?? '' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Lainnya</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">ID Prospek</span>
                        <span class="font-semibold text-gray-900">#{{ $prospek->id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-semibold text-gray-900">{{ $prospek->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Diperbarui</span>
                        <span class="font-semibold text-gray-900">{{ $prospek->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($prospek->konsumen)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Konsumen</span>
                            <span class="font-semibold text-emerald-600">{{ $prospek->konsumen->nama_lengkap }}</span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
