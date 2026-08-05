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

        <div x-data='simulasiData(@json($units))' x-init="init()" class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Form Simulasi Pembayaran">
                    <div class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="id_unit" class="block text-sm font-semibold text-gray-700 mb-1">Unit Rumah
                                    <span class="text-red-500">*</span></label>
                                <select id="id_unit" x-model="idUnit" x-on:change="updateUnit()"
                                    class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}" data-harga="{{ $unit->harga_jual }}">
                                            {{ $unit->kode_unit }} - {{ $unit->tipe_rumah }} (Rp
                                            {{ number_format($unit->harga_jual, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="metode_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Metode
                                    Pembayaran <span class="text-red-500">*</span></label>
                                <select id="metode_pembayaran" x-model="method" x-on:change="updateMethod()"
                                    class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                                    <option value="kpr">KPR</option>
                                    <option value="cash_bertahap">Cash Bertahap</option>
                                    <option value="cash_keras">Cash Keras</option>
                                </select>
                            </div>
                        </div>

                        <template x-if="hasKprOrCashBertahap">
                            <div class="grid gap-4 md:grid-cols-3">
                                <div>
                                    <label for="dp_persen" class="block text-sm font-semibold text-gray-700 mb-1">DP
                                        (%)</label>
                                    <input id="dp_persen" type="number" step="0.1" x-model="dpPersen" min="0"
                                        max="100"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                        placeholder="Misal 20" />
                                </div>
                                <div>
                                    <label for="tenor_tahun" class="block text-sm font-semibold text-gray-700 mb-1">Tenor
                                        (tahun)</label>
                                    <input id="tenor_tahun" type="number" x-model="tenorTahun" min="1"
                                        max="30"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                        placeholder="Misal 10" />
                                </div>
                                <div x-show="method === 'kpr'" x-transition>
                                    <label for="suku_bunga" class="block text-sm font-semibold text-gray-700 mb-1">Suku
                                        Bunga (%)</label>
                                    <input id="suku_bunga" type="number" step="0.01" x-model="sukuBunga" min="0"
                                        max="50"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                        placeholder="Misal 12" />
                                </div>
                            </div>
                        </template>

                        <template x-if="method === 'cash_keras'">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="diskon_persen" class="block text-sm font-semibold text-gray-700 mb-1">Diskon
                                        Cash Keras (%)</label>
                                    <input id="diskon_persen" type="number" step="0.1" x-model="diskonPersen"
                                        min="0" max="100"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary"
                                        placeholder="Opsional" />
                                </div>
                            </div>
                        </template>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button type="button" x-on:click="calculate()"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                Hitung
                            </button>
                        </div>

                        <template x-if="Object.keys(errors).length">
                            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                                <template x-for="(message, field) in errors" :key="field">
                                    <p x-text="message"></p>
                                </template>
                            </div>
                        </template>
                    </div>
                </x-card>

                <template x-if="hasResult">
                    <x-card title="Hasil Simulasi">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4">
                                <p class="text-sm font-semibold text-gray-600">Metode</p>
                                <p class="text-xl font-bold text-gray-900" x-text="resultLabel"></p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4">
                                <p class="text-sm font-semibold text-gray-600">Harga Rumah</p>
                                <p class="text-xl font-bold text-gray-900" x-text="formattedCurrency(result.harga_rumah)">
                                </p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4">
                                <p class="text-sm font-semibold text-gray-600">DP Nominal</p>
                                <p class="text-xl font-bold text-gray-900" x-text="formattedCurrency(result.dp_nominal)">
                                </p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4">
                                <p class="text-sm font-semibold text-gray-600">Cicilan Bulanan</p>
                                <p class="text-xl font-bold text-gray-900"
                                    x-text="formattedCurrency(result.cicilan_bulanan)"></p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4"
                                x-show="result.plafon !== undefined">
                                <p class="text-sm font-semibold text-gray-600">Plafon KPR</p>
                                <p class="text-xl font-bold text-gray-900" x-text="formattedCurrency(result.plafon)"></p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4"
                                x-show="result.total_bunga !== undefined">
                                <p class="text-sm font-semibold text-gray-600">Total Bunga</p>
                                <p class="text-xl font-bold text-gray-900" x-text="formattedCurrency(result.total_bunga)">
                                </p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-gray-200 bg-white p-4"
                                x-show="result.diskon_persen !== undefined">
                                <p class="text-sm font-semibold text-gray-600">Diskon (%)</p>
                                <p class="text-xl font-bold text-gray-900" x-text="result.diskon_persen + '%' "></p>
                            </div>
                        </div>
                    </x-card>
                </template>
            </div>

            <div class="space-y-6">
                <x-card title="Perbandingan 3 Metode">
                    <div class="space-y-4">
                        <template x-for="(item, key) in comparison" :key="key">
                            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700"
                                            x-text="item.metode ? item.metode.toUpperCase().replace('_', ' ') : key"></p>
                                        <p class="text-xs text-gray-500" x-show="item.error" x-text="item.error"></p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900" x-show="!item.error"
                                        x-text="formattedCurrency(item.total_pembayaran)"></p>
                                </div>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <template x-if="!item.error">
                                        <div>
                                            <p class="text-xs uppercase tracking-wider text-gray-500">Cicilan / Plafon</p>
                                            <p class="text-sm text-gray-900"
                                                x-text="item.cicilan_bulanan ? formattedCurrency(item.cicilan_bulanan) : '-'">
                                            </p>
                                        </div>
                                    </template>
                                    <template x-if="!item.error">
                                        <div>
                                            <p class="text-xs uppercase tracking-wider text-gray-500">Total Bunga</p>
                                            <p class="text-sm text-gray-900"
                                                x-text="item.total_bunga ? formattedCurrency(item.total_bunga) : '-'"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function simulasiData(units) {
            return {
                units: units || [],
                idUnit: '',
                method: 'kpr',
                dpPersen: '',
                tenorTahun: '',
                sukuBunga: '',
                diskonPersen: '',
                result: null,
                comparison: {},
                errors: {},
                isLoading: false,

                init() {
                    if (this.units.length) {
                        this.idUnit = this.units[0].id.toString();
                        this.updateUnit();
                    }
                },

                get hasKprOrCashBertahap() {
                    return this.method === 'kpr' || this.method === 'cash_bertahap';
                },

                get hasResult() {
                    return this.result !== null;
                },

                get resultLabel() {
                    return {
                        kpr: 'KPR',
                        cash_bertahap: 'Cash Bertahap',
                        cash_keras: 'Cash Keras',
                    } [this.method] || 'Hasil';
                },

                updateUnit() {
                    this.errors = {};
                    this.result = null;
                    this.comparison = {};
                },

                updateMethod() {
                    if (this.method === 'cash_keras') {
                        this.dpPersen = '';
                        this.tenorTahun = '';
                        this.sukuBunga = '';
                    }
                    this.errors = {};
                    this.result = null;
                    this.comparison = {};
                },

                formattedCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }).format(Number(value ?? 0));
                },

                async calculate() {
                    this.errors = {};
                    this.result = null;
                    this.comparison = {};
                    this.isLoading = true;

                    try {
                        const response = await fetch('{{ route('admin.simulasi.hitung') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                id_unit: this.idUnit,
                                metode_pembayaran: this.method,
                                dp_persen: this.dpPersen || null,
                                tenor_tahun: this.tenorTahun || null,
                                suku_bunga: this.sukuBunga || null,
                                diskon_persen: this.diskonPersen || null,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (data.errors) {
                                this.errors = Object.fromEntries(Object.entries(data.errors).map(([key, value]) => [key,
                                    value[0]
                                ]));
                            } else {
                                this.errors = {
                                    general: data.message || 'Terjadi kesalahan saat menghitung simulasi.'
                                };
                            }
                            return;
                        }

                        this.result = data.hasil;
                        this.comparison = data.perbandingan;
                    } catch (error) {
                        this.errors = {
                            general: 'Gagal memproses permintaan. Coba lagi.'
                        };
                    } finally {
                        this.isLoading = false;
                    }
                },
            };
        }
    </script>
@endpush
