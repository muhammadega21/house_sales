@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if($prospek->status_prospek->value === 'jadi_konsumen')
        <x-card>
            <div class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a2 2 0 00-2-2H6a2 2 0 00-2 2v2m16 0H6" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Prospek sudah dikonversi</h3>
                <p class="mt-2 text-sm text-gray-500">Prospek ini sudah menjadi konsumen dan tidak dapat diedit lagi.</p>
                <a href="{{ route('admin.prospek.index') }}" class="mt-6 inline-flex items-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    Kembali ke Daftar Prospek
                </a>
            </div>
        </x-card>
    @else
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Prospek</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data prospek #{{ $prospek->id }}.</p>
        </div>
    </div>

    <x-card>
        <form action="{{ route('admin.prospek.update', $prospek->id) }}" method="POST">
            @csrf
            @method('PUT')
            @php
                $sumberOptions = collect(\App\Enums\SumberProspek::cases())->mapWithKeys(fn($s) => [$s->value => $s->icon() . ' ' . $s->label()]);
                $statusOptions = collect(\App\Enums\StatusProspek::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()]);
                $marketingOptions = \App\Models\User::marketing()->aktif()->orderBy('nama_lengkap')->get()->mapWithKeys(fn($m) => [$m->id => $m->nama_lengkap]);
            @endphp
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_prospek" label="Nama Prospek" :value="$prospek->nama_prospek" :required="true" maxlength="100" />
                <x-form-input name="no_hp" label="Nomor HP" :value="$prospek->no_hp" :required="true" maxlength="15" />
                <x-form-input name="email" label="Email" type="email" :value="$prospek->email" />
                <x-form-select name="sumber_prospek" label="Sumber Prospek" :options="$sumberOptions" :selected="$prospek->sumber_prospek?->value" />
                <x-form-input name="tanggal_prospek" label="Tanggal Prospek" type="date" :value="$prospek->tanggal_prospek->format('Y-m-d')" :required="true" />
                <x-form-select name="status_prospek" label="Status Prospek" :options="$statusOptions" :selected="$prospek->status_prospek->value" :required="true" />
                <x-form-select name="id_marketing" label="Marketing PIC" :options="$marketingOptions" :selected="$prospek->id_marketing" :required="true" />
            </div>
            <x-form-textarea name="catatan" label="Catatan" rows="3" :value="$prospek->catatan" />

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('admin.prospek.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-card>
    @endif
</div>
@endsection
