<div x-show="hasil && hasil.amortisasi" class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Tabel Amortisasi</h3>
            <p class="text-sm text-gray-500">Daftar cicilan bulanan pertama untuk KPR.</p>
        </div>
        <button type="button" x-data="{ open: false }" @click="open = !open"
            class="rounded-full border border-gray-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100">
            <span x-show="!open">Lihat semua</span>
            <span x-show="open">Sembunyikan</span>
        </button>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-left text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">Bulan</th>
                    <th class="px-4 py-3">Cicilan</th>
                    <th class="px-4 py-3">Pokok</th>
                    <th class="px-4 py-3">Bunga</th>
                    <th class="px-4 py-3">Sisa Pokok</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in hasil.amortisasi.slice(0, 12)" :key="index">
                    <tr class="border-b border-gray-100 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900" x-text="row.bulan"></td>
                        <td class="px-4 py-3" x-text="formattedCurrency(row.cicilan)"></td>
                        <td class="px-4 py-3" x-text="formattedCurrency(row.pokok)"></td>
                        <td class="px-4 py-3" x-text="formattedCurrency(row.bunga)"></td>
                        <td class="px-4 py-3" x-text="formattedCurrency(row.sisa_pokok)"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-right">
        <button type="button"
            class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Lihat
            semua</button>
    </div>
</div>
