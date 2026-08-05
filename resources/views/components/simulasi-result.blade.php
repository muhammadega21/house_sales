<div x-show="hasil" class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Hasil Simulasi</h2>
            <p class="text-sm text-gray-500">Ringkasan estimasi cicilan dan total pembayaran.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800"
            x-text="hasil.metode.toUpperCase()"></span>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-sm text-slate-500">Cicilan Bulanan</p>
            <p class="mt-2 text-3xl font-bold text-slate-900" x-text="formattedCurrency(hasil.cicilan_bulanan)"></p>
        </div>
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-sm text-slate-500">Total Pembayaran</p>
            <p class="mt-2 text-3xl font-bold text-slate-900" x-text="formattedCurrency(hasil.total_pembayaran)"></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4">
            <p class="text-sm text-slate-500">Total Bunga</p>
            <p class="mt-2 text-xl font-semibold text-slate-900" x-text="formattedCurrency(hasil.total_bunga)"></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4">
            <p class="text-sm text-slate-500">DP Dibayar</p>
            <p class="mt-2 text-xl font-semibold text-slate-900" x-text="formattedCurrency(hasil.dp_nominal)"></p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-white border border-gray-200 p-4">
            <p class="text-sm text-slate-500">Harga Rumah</p>
            <p class="mt-2 text-base font-semibold text-slate-900" x-text="formattedCurrency(hasil.harga_rumah)"></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4">
            <p class="text-sm text-slate-500">DP</p>
            <p class="mt-2 text-base font-semibold text-slate-900"><span x-text="hasil.dp_persen"></span>% / <span
                    x-text="formattedCurrency(hasil.dp_nominal)"></span></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4" x-show="hasil.plafon !== undefined">
            <p class="text-sm text-slate-500">Plafon KPR</p>
            <p class="mt-2 text-base font-semibold text-slate-900" x-text="formattedCurrency(hasil.plafon)"></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4">
            <p class="text-sm text-slate-500">Tenor</p>
            <p class="mt-2 text-base font-semibold text-slate-900" x-text="hasil.tenor_tahun + ' tahun'"></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4"
            x-show="hasil.suku_bunga !== undefined && hasil.suku_bunga !== null">
            <p class="text-sm text-slate-500">Suku Bunga</p>
            <p class="mt-2 text-base font-semibold text-slate-900" x-text="hasil.suku_bunga + '%' "></p>
        </div>
        <div class="rounded-2xl bg-white border border-gray-200 p-4"
            x-show="hasil.diskon_persen !== undefined && hasil.diskon_persen !== null">
            <p class="text-sm text-slate-500">Diskon</p>
            <p class="mt-2 text-base font-semibold text-slate-900" x-text="hasil.diskon_persen + '%' "></p>
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 p-4 text-sm text-blue-700">
        <p>Simulasi ini bersifat estimasi dan tidak mengikat. Suku bunga aktual ditentukan oleh pihak bank.</p>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <button type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition"
            @click="$dispatch('save-simulasi')">Simpan Simulasi</button>
        <button type="button"
            class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition"
            @click="$dispatch('export-pdf')">Export PDF</button>
    </div>
</div>
