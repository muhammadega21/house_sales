@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
                <p class="text-sm text-gray-500">Atur nilai default untuk simulasi KPR, cash keras, dan batas DP.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card title="Pengaturan Default Simulasi">
            <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="grid gap-6 md:grid-cols-2">
                @csrf
                @method('PUT')

                <x-form-input name="default_kpr_bunga" label="Default Suku Bunga KPR (%)" type="number" step="0.01"
                    :value="old('default_kpr_bunga', $settings['default_kpr_bunga'] ?? '8')" required />

                <x-form-input name="default_cash_keras_diskon" label="Default Diskon Cash Keras (%)" type="number"
                    step="0.01" :value="old('default_cash_keras_diskon', $settings['default_cash_keras_diskon'] ?? '0')" required />

                <x-form-input name="dp_subsidi_min_persen" label="DP Subsidi Minimum (%)" type="number" step="0.1"
                    :value="old('dp_subsidi_min_persen', $settings['dp_subsidi_min_persen'] ?? '1')" required />

                <x-form-input name="dp_subsidi_max_persen" label="DP Subsidi Maksimum (%)" type="number" step="0.1"
                    :value="old('dp_subsidi_max_persen', $settings['dp_subsidi_max_persen'] ?? '5')" required />

                <x-form-input name="dp_non_subsidi_min_persen" label="DP Non-Subsidi Minimum (%)" type="number"
                    step="0.1" :value="old('dp_non_subsidi_min_persen', $settings['dp_non_subsidi_min_persen'] ?? '10')" required />

                <x-form-input name="dp_non_subsidi_max_persen" label="DP Non-Subsidi Maksimum (%)" type="number"
                    step="0.1" :value="old('dp_non_subsidi_max_persen', $settings['dp_non_subsidi_max_persen'] ?? '30')" required />

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
