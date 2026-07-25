@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Prospek</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data prospek #{{ $prospek->id }}.</p>
        </div>
    </div>

    @php $isJadiKonsumen = $prospek->status_prospek->value === 'jadi_konsumen'; @endphp

    @if($isJadiKonsumen)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4">
            <div class="flex gap-3">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-emerald-800">Prospek ini telah menjadi konsumen.</p>
                    <p class="mt-1 text-sm text-emerald-700">Data tidak dapat diubah lagi.</p>
                </div>
            </div>
        </div>
    @endif

    <x-card>
        <form action="{{ route('marketing.prospek.update', $prospek->id) }}" method="POST">
            @csrf
            @method('PUT')
            @php
                $sumberOptions = collect(\App\Enums\SumberProspek::cases())->mapWithKeys(fn($s) => [$s->value => $s->icon() . ' ' . $s->label()]);
                $statusOptions = collect(\App\Enums\StatusProspek::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()]);
            @endphp
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_prospek" label="Nama Prospek" :value="$prospek->nama_prospek" :required="true" maxlength="100" :disabled="$isJadiKonsumen" />
                <x-form-input name="no_hp" label="Nomor HP" :value="$prospek->no_hp" :required="true" maxlength="15" :disabled="$isJadiKonsumen" />
                <x-form-input name="email" label="Email" type="email" :value="$prospek->email" :disabled="$isJadiKonsumen" />
                <x-form-select name="sumber_prospek" label="Sumber Prospek" :options="$sumberOptions" :selected="$prospek->sumber_prospek?->value" :disabled="$isJadiKonsumen" />
                <x-form-input name="tanggal_prospek" label="Tanggal Prospek" type="date" :value="$prospek->tanggal_prospek->format('Y-m-d')" :required="true" :disabled="$isJadiKonsumen" />
                <x-form-select name="status_prospek" label="Status Prospek" :options="$statusOptions" :selected="$prospek->status_prospek->value" :required="true" :disabled="$isJadiKonsumen" />
            </div>
            <x-form-textarea name="catatan" label="Catatan" rows="3" :value="$prospek->catatan" :disabled="$isJadiKonsumen" />

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('marketing.prospek.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" @if($isJadiKonsumen) disabled @endif
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark disabled:opacity-50">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
