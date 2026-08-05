@props([
    'units' => [],
    'defaultUnit' => null,
    'konsumenList' => [],
    'defaultBunga' => 8,
    'defaultDiskon' => 0,
    'dpLimits' => [
        'subsidi' => ['min' => 1, 'max' => 5],
        'non_subsidi' => ['min' => 10, 'max' => 30],
    ],
])

<div x-data='simulasiForm({
    units: @json($units),
    konsumenList: @json($konsumenList),
    defaultUnit: @json($defaultUnit),
    defaultBunga: @json($defaultBunga),
    defaultDiskon: @json($defaultDiskon),
    dpLimits: @json($dpLimits),
})'
    x-init="init()" @save-simulasi="saveSimulation()" @export-pdf="exportPdf()" class="space-y-6">

    <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Form Simulasi Pembayaran</h2>
                <p class="text-sm text-gray-500">Pilih unit, metode, dan sesuaikan parameter untuk melihat estimasi
                    cicilan.</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm">
                <span class="font-semibold">Realtime</span>
                <span class="text-gray-500">(Debounce 500ms)</span>
            </div>
        </div>

        <div class="mt-6 grid gap-5">
            <div>
                <label for="simulasi_unit" class="block text-sm font-semibold text-gray-700 mb-2">Unit Rumah <span
                        class="text-red-500">*</span></label>
                <select id="simulasi_unit" x-model="unitId" x-on:change="updateUnit()"
                    class="block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">Pilih unit tersedia</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" data-harga="{{ $unit->harga_jual }}">
                            {{ $unit->kode_unit }} — {{ $unit->tipe_rumah }}
                            ({{ number_format($unit->harga_jual, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>

                <template x-if="unitInfo">
                    <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700">
                        <p class="font-semibold text-slate-900">Informasi Unit</p>
                        <p>Kode: <span class="font-medium" x-text="unitInfo.kode_unit"></span></p>
                        <p>Tipe: <span class="font-medium" x-text="unitInfo.tipe_rumah"></span></p>
                        <p>Kategori: <span class="font-medium" x-text="formatKategori(unitInfo.kategori)"></span></p>
                        <p>Harga: <span class="font-medium" x-text="formatCurrency(unitInfo.harga_jual)"></span></p>
                    </div>
                </template>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Metode Pembayaran</p>
                <div class="grid gap-3 sm:grid-cols-3">
                    <label
                        class="cursor-pointer rounded-2xl border p-4 text-sm font-medium transition hover:border-primary"
                        :class="metode === 'kpr' ? 'border-primary bg-blue-50 text-blue-900' :
                            'border-gray-200 bg-white text-slate-700'">
                        <input type="radio" name="metode" value="kpr" x-model="metode" class="sr-only" />
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🏦</span>
                            <span>KPR</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Kredit Pemilikan Rumah</p>
                    </label>
                    <label
                        class="cursor-pointer rounded-2xl border p-4 text-sm font-medium transition hover:border-primary"
                        :class="metode === 'cash_bertahap' ? 'border-primary bg-blue-50 text-blue-900' :
                            'border-gray-200 bg-white text-slate-700'">
                        <input type="radio" name="metode" value="cash_bertahap" x-model="metode" class="sr-only" />
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📅</span>
                            <span>Cash Bertahap</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Tanpa bunga, cicilan bertahap</p>
                    </label>
                    <label
                        class="cursor-pointer rounded-2xl border p-4 text-sm font-medium transition hover:border-primary"
                        :class="metode === 'cash_keras' ? 'border-primary bg-blue-50 text-blue-900' :
                            'border-gray-200 bg-white text-slate-700'">
                        <input type="radio" name="metode" value="cash_keras" x-model="metode" class="sr-only" />
                        <div class="flex items-center gap-2">
                            <span class="text-xl">💰</span>
                            <span>Cash Keras</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Pembayaran lunas sekaligus</p>
                    </label>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-sm font-semibold text-slate-700">DP (%)</label>
                        <span class="text-sm text-slate-500" x-text="formatDecimal(dpPersen) + '%' "></span>
                    </div>
                    <input type="range" min="0" max="100" step="0.5" x-model.number="dpPersen"
                        @input="validateDp()" class="mt-3 w-full" />
                    <p class="mt-3 text-sm text-slate-500">DP <span x-text="formatDecimal(dpPersen)"></span>% = <span
                            class="font-semibold" x-text="formatCurrency(dpNominal)"></span></p>
                    <template x-if="dpNote">
                        <p class="mt-2 text-xs text-amber-600" x-text="dpNote"></p>
                    </template>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4" x-show="showTenor" x-transition>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-sm font-semibold text-slate-700">Tenor (tahun)</label>
                        <span class="text-sm text-slate-500" x-text="tenor + ' tahun (' + totalMonths + ' bulan)'">
                        </span>
                    </div>
                    <input type="range" min="1" max="30" step="1" x-model.number="tenor"
                        class="mt-3 w-full" />
                    <template x-if="metode === 'kpr'">
                        <p class="mt-3 text-sm text-slate-500">Tenor KPR maksimal 20 tahun.</p>
                    </template>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-4" x-show="metode === 'kpr'" x-transition>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Suku Bunga (%)</label>
                    <input type="number" min="0" step="0.01" x-model.number="bunga"
                        class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm text-slate-800 focus:border-primary focus:ring-primary" />
                    <p class="mt-2 text-xs text-slate-500">Suku bunga aktual ditentukan oleh bank.</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4" x-show="metode === 'cash_keras'"
                    x-transition>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Diskon Cash Keras (%)</label>
                    <input type="number" min="0" step="0.1" x-model.number="diskon"
                        class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm text-slate-800 focus:border-primary focus:ring-primary" />
                    <p class="mt-2 text-xs text-slate-500">Diskon khusus untuk pembayaran cash keras.</p>
                </div>
            </div>

            <div>
                <label for="konsumen_simulasi" class="block text-sm font-semibold text-gray-700 mb-2">Konsumen
                    (opsional)</label>
                <select id="konsumen_simulasi" x-model="konsumenId"
                    class="block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">Pilih konsumen</option>
                    <template x-for="(label, id) in konsumenList" :key="id">
                        <option :value="id" x-text="label"></option>
                    </template>
                </select>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <button type="button" @click="hitungSimulasi()" :disabled="loading"
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-text="loading ? 'Memuat...' : 'Hitung Simulasi'"></span>
                </button>
                <button type="button" @click="saveSimulation()"
                    class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition"
                    :disabled="!hasil">
                    Simpan Simulasi
                </button>
                <button type="button" @click="exportPdf()"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition"
                    :disabled="!hasil">Export PDF</button>
                <button type="button" @click="viewComparison()"
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition"
                    :disabled="!hasil">Lihat Perbandingan</button>
            </div>

            <template x-if="errorMessage">
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p x-text="errorMessage"></p>
                </div>
            </template>

            <template x-if="hasil">
                <div class="mt-6 grid gap-6 xl:grid-cols-[2fr_1fr]">
                    <div class="space-y-6">
                        <x-simulasi-result />

                        <template x-if="hasil.metode === 'kpr'">
                            <x-amortisasi-table />
                        </template>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Perbandingan Metode</h2>
                                    <p class="text-sm text-gray-500">Bandingkan total pembayaran untuk ketiga metode.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <template x-for="(item, key) in perbandingan" :key="key">
                                    <div class="rounded-2xl border border-gray-200 bg-slate-50 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900"
                                                    x-text="item.metode ? item.metode.toUpperCase().replace('_', ' ') : key">
                                                </p>
                                                <p class="text-xs text-slate-500"
                                                    x-show="item.total_bunga !== undefined">Total bunga: <span
                                                        x-text="formattedCurrency(item.total_bunga)"></span></p>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-900"
                                                x-text="formattedCurrency(item.total_pembayaran)"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            @push('scripts')
                <script>
                    function simulasiForm(config) {
                        return {
                            units: config.units || [],
                            konsumenList: config.konsumenList || {},
                            unitId: config.defaultUnit || '',
                            unitInfo: null,
                            metode: 'kpr',
                            dpPersen: 10,
                            tenor: 15,
                            bunga: config.defaultBunga ?? 8,
                            diskon: config.defaultDiskon ?? 0,
                            konsumenId: '',
                            hasil: null,
                            perbandingan: {},
                            errorMessage: '',
                            loading: false,
                            debounceTimeout: null,

                            init() {
                                // Only initialize when a unit was explicitly provided (defaultUnit)
                                // or when the component was initialized with a non-empty unitId.
                                // Do NOT auto-select the first available unit to avoid showing 
                                // unit info before the user makes a choice.
                                if (this.unitId !== '') {
                                    this.updateUnit();
                                }
                            },

                            get availableUnits() {
                                return this.units.filter(u => {
                                    if (!u || u.status_unit === undefined || u.status_unit === null) {
                                        return false;
                                    }

                                    const status = typeof u.status_unit === 'object' ? u.status_unit.value : u.status_unit;
                                    return String(status) === 'tersedia';
                                });
                            },

                            get showTenor() {
                                return this.metode === 'kpr' || this.metode === 'cash_bertahap';
                            },

                            get totalMonths() {
                                return this.tenor * 12;
                            },

                            get dpNominal() {
                                if (!this.unitInfo) return 0;
                                return Math.round(this.unitInfo.harga_jual * (this.dpPersen / 100));
                            },

                            get dpNote() {
                                if (!this.unitInfo) return '';
                                const kategori = this.unitInfo.kategori;
                                const limits = kategori === 'subsidi' ? this.dpLimits.subsidi : this.dpLimits.non_subsidi;
                                return `DP untuk rumah ${kategori === 'subsidi' ? 'subsidi' : 'non-subsidi'} harus antara ${limits.min}% - ${limits.max}%.`;
                            },

                            formatKategori(value) {
                                return value === 'subsidi' ? 'Subsidi' : 'Non-Subsidi';
                            },

                            updateUnit() {
                                this.unitInfo = this.units.find(u => u.id.toString() === this.unitId.toString()) || null;
                                if (!this.unitInfo) {
                                    this.hasil = null;
                                    this.perbandingan = {};
                                    return;
                                }
                                const limits = this.unitInfo.kategori === 'subsidi' ? this.dpLimits.subsidi : this.dpLimits.non_subsidi;
                                if (this.dpPersen === '' || Number(this.dpPersen) < limits.min || Number(this.dpPersen) > limits.max) {
                                    this.dpPersen = limits.min;
                                }
                                this.errorMessage = '';
                                this.hitungSimulasi();
                            },

                            validateDp() {
                                if (!this.unitInfo) return;
                                const limits = this.unitInfo.kategori === 'subsidi' ? this.dpLimits.subsidi : this.dpLimits.non_subsidi;
                                if (Number(this.dpPersen) < limits.min) this.dpPersen = limits.min;
                                if (Number(this.dpPersen) > limits.max) this.dpPersen = limits.max;
                                this.hitungSimulasi();
                            },

                            formatCurrency(value) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0,
                                }).format(Number(value ?? 0));
                            },
                            formattedCurrency(value) {
                                return this.formatCurrency(value);
                            },

                            formatDecimal(value) {
                                return Number(value).toFixed(1);
                            },

                            async hitungSimulasi() {
                                if (!this.unitId) {
                                    this.errorMessage = 'Pilih unit terlebih dahulu.';
                                    return;
                                }
                                this.loading = true;
                                this.errorMessage = '';

                                try {
                                    const response = await fetch('/marketing/simulasi/hitung', {
                                        method: 'POST',
                                        credentials: 'same-origin',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                        body: JSON.stringify({
                                            id_unit: this.unitId,
                                            metode_pembayaran: this.metode,
                                            dp_persen: this.dpPersen,
                                            tenor_tahun: this.showTenor ? this.tenor : null,
                                            suku_bunga: this.metode === 'kpr' ? this.bunga : null,
                                            diskon_persen: this.metode === 'cash_keras' ? this.diskon : null,
                                            id_konsumen: this.konsumenId || null,
                                        }),
                                    });

                                    const ct = response.headers.get('content-type') || '';
                                    let data;
                                    if (ct.includes('application/json')) {
                                        data = await response.json();
                                    } else {
                                        data = {
                                            message: await response.text()
                                        };
                                    }

                                    if (!response.ok) {
                                        this.hasil = null;
                                        this.perbandingan = {};

                                        const errorMessage = data.errors ?
                                            Object.values(data.errors).flat()[0] :
                                            data.message || `Server mengembalikan status ${response.status}.`;

                                        this.errorMessage = errorMessage ||
                                            'Gagal menghitung simulasi. Periksa input dan coba lagi.';
                                        return;
                                    }

                                    this.hasil = data.hasil;
                                    this.perbandingan = data.perbandingan || {};
                                } catch (error) {
                                    this.hasil = null;
                                    if (error instanceof Error && error.message) {
                                        this.errorMessage = error.message.includes('Failed to fetch') ?
                                            'Gagal terhubung ke server. Periksa koneksi internet Anda.' :
                                            error.message;
                                    } else {
                                        this.errorMessage = 'Gagal terhubung ke server. Coba lagi nanti.';
                                    }
                                } finally {
                                    this.loading = false;
                                }
                            },

                            debouncedCalculate() {
                                clearTimeout(this.debounceTimeout);
                                this.debounceTimeout = setTimeout(() => this.hitungSimulasi(), 500);
                            },

                            async saveSimulation() {
                                if (!this.hasil) return;
                                try {
                                    const response = await fetch('/marketing/simulasi/simpan', {
                                        method: 'POST',
                                        credentials: 'same-origin',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                        body: JSON.stringify({
                                            id_unit: this.unitId,
                                            metode_pembayaran: this.metode,
                                            dp_persen: this.dpPersen,
                                            tenor_tahun: this.showTenor ? this.tenor : null,
                                            suku_bunga: this.metode === 'kpr' ? this.bunga : null,
                                            diskon_persen: this.metode === 'cash_keras' ? this.diskon : null,
                                            id_konsumen: this.konsumenId || null,
                                        }),
                                    });
                                    const ct2 = response.headers.get('content-type') || '';
                                    let data;
                                    if (ct2.includes('application/json')) {
                                        data = await response.json();
                                    } else {
                                        const txt = await response.text();
                                        throw new Error('Unexpected server response: ' + txt.slice(0, 300));
                                    }
                                    if (!response.ok) {
                                        this.errorMessage = data.errors ? Object.values(data.errors)[0][0] : (data.message ||
                                            'Gagal menyimpan simulasi.');
                                        return;
                                    }
                                    alert(data.message || 'Simulasi berhasil disimpan.');
                                } catch (error) {
                                    this.errorMessage = 'Gagal menyimpan simulasi.';
                                }
                            },

                            buildQueryParams() {
                                const params = new URLSearchParams();
                                params.set('id_unit', this.unitId);
                                params.set('dp_persen', this.dpPersen);
                                params.set('tenor_tahun', this.tenor);
                                params.set('suku_bunga', this.bunga);
                                params.set('diskon_persen', this.diskon);

                                if (this.konsumenId) {
                                    params.set('id_konsumen', this.konsumenId);
                                }

                                return params.toString();
                            },

                            viewComparison() {
                                if (!this.unitId) {
                                    this.errorMessage = 'Pilih unit terlebih dahulu.';
                                    return;
                                }

                                window.location.href = '/marketing/simulasi/perbandingan?' + this.buildQueryParams();
                            },

                            exportPdf() {
                                if (!this.unitId) {
                                    this.errorMessage = 'Pilih unit terlebih dahulu.';
                                    return;
                                }

                                window.open('/marketing/simulasi/export-pdf?' + this.buildQueryParams(), '_blank');
                            },
                        };
                    }
                </script>
            @endpush
