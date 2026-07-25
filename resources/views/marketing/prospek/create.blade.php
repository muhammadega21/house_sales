@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Prospek</h1>
            <p class="mt-1 text-sm text-gray-500">Input data calon konsumen baru.</p>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4">
        <div class="flex gap-3">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-800">Prospek adalah calon konsumen yang belum melakukan booking.</p>
                <p class="mt-1 text-sm text-blue-700">Lengkapi data untuk memudahkan follow-up.</p>
            </div>
        </div>
    </div>

    <x-card>
        <form action="{{ route('marketing.prospek.store') }}" method="POST">
            @csrf
            @php
                $sumberOptions = collect(\App\Enums\SumberProspek::cases())->mapWithKeys(fn($s) => [$s->value => $s->icon() . ' ' . $s->label()]);
                $statusOptions = collect(\App\Enums\StatusProspek::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()]);
            @endphp
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_prospek" label="Nama Prospek" :required="true" maxlength="100" placeholder="Nama lengkap calon konsumen" />
                <x-form-input name="no_hp" label="Nomor HP" :required="true" maxlength="15" placeholder="0812xxxxxxxx" />
                <x-form-input name="email" label="Email" type="email" placeholder="contoh@email.com" />
                <x-form-select name="sumber_prospek" label="Sumber Prospek" :options="$sumberOptions" />
                <x-form-input name="tanggal_prospek" label="Tanggal Prospek" type="date" :required="true" :value="old('tanggal_prospek', now()->format('Y-m-d'))" />
                <x-form-select name="status_prospek" label="Status Prospek" :options="$statusOptions" :selected="old('status_prospek', \App\Enums\StatusProspek::Baru->value)" :required="true" />
            </div>
            <x-form-textarea name="catatan" label="Catatan" rows="3" placeholder="Catatan follow-up, preferensi, dll." />

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('marketing.prospek.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" name="_save_and_new" value="1"
                        class="inline-flex items-center justify-center rounded-lg border border-primary px-4 py-2.5 text-sm font-semibold text-primary transition hover:bg-primary/5">
                    Simpan & Tambah Lagi
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    Simpan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
