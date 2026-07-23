@php
    $isEdit = isset($unitRumah);
    $isSold = $isEdit && $unitRumah->status_unit === \App\Enums\StatusUnit::Dijual;
    $availability = old('jenis_ketersediaan', $isEdit ? $unitRumah->jenis_ketersediaan->value : 'ready_stock');
@endphp
@if($isSold)
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Unit sudah terjual. Semua data unit dikunci dan tidak dapat diubah.</div>
@endif
<fieldset {{ $isSold ? 'disabled' : '' }} x-data="{ availability: '{{ $availability }}', tanah: '{{ old('luas_tanah', $isEdit ? $unitRumah->luas_tanah : '') }}', bangunan: '{{ old('luas_bangunan', $isEdit ? $unitRumah->luas_bangunan : '') }}' }">
    <div class="grid gap-x-6 md:grid-cols-2">
        <x-form-select name="id_perumahan" label="Perumahan" :options="$perumahan->all()" :selected="$isEdit ? $unitRumah->id_perumahan : request('id_perumahan')" :required="true" />
        <x-form-input name="kode_unit" label="Kode Unit" :value="$isEdit ? $unitRumah->kode_unit : null" :required="true" maxlength="20" placeholder="Contoh: A-01" />
        <x-form-input name="tipe_rumah" label="Tipe Rumah" :value="$isEdit ? $unitRumah->tipe_rumah : null" :required="true" maxlength="50" placeholder="Contoh: 36/60" />
        <x-form-select name="kategori" label="Kategori" :options="['subsidi' => 'Subsidi', 'non_subsidi' => 'Non-Subsidi']" :selected="$isEdit ? $unitRumah->kategori->value : null" :required="true" />
        <div class="mb-4"><label for="jenis_ketersediaan" class="mb-1.5 block text-sm font-semibold text-gray-700">Ketersediaan <span class="text-red-500">*</span></label><select x-model="availability" name="jenis_ketersediaan" id="jenis_ketersediaan" required class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm shadow-xs focus:border-primary focus:ring-2 focus:ring-primary"><option value="ready_stock">Ready Stock</option><option value="indent">Indent</option></select>@error('jenis_ketersediaan')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
        <div x-show="availability === 'indent'" x-cloak><x-form-input name="tanggal_selesai_bangun" label="Tanggal Selesai Bangun" type="date" :value="$isEdit && $unitRumah->tanggal_selesai_bangun ? $unitRumah->tanggal_selesai_bangun->format('Y-m-d') : null" /></div>
        <x-form-input x-model="tanah" name="luas_tanah" label="Luas Tanah (m²)" type="number" step="0.01" min="0" :value="$isEdit ? $unitRumah->luas_tanah : null" :required="true" />
        <div><x-form-input x-model="bangunan" name="luas_bangunan" label="Luas Bangunan (m²)" type="number" step="0.01" min="0" :value="$isEdit ? $unitRumah->luas_bangunan : null" :required="true" /><p x-show="Number(bangunan) > Number(tanah) && tanah !== ''" class="-mt-3 mb-4 text-xs font-semibold text-red-600">Luas bangunan tidak boleh lebih dari luas tanah.</p></div>
        <x-form-input name="jumlah_kamar_tidur" label="Jumlah Kamar Tidur" type="number" min="0" :value="$isEdit ? $unitRumah->jumlah_kamar_tidur : null" />
        <x-form-input name="jumlah_kamar_mandi" label="Jumlah Kamar Mandi" type="number" min="0" :value="$isEdit ? $unitRumah->jumlah_kamar_mandi : null" />
        <x-form-input name="harga_jual" label="Harga Jual (Rp)" type="number" min="1" :value="$isEdit ? $unitRumah->harga_jual : null" :required="true" />
        <x-form-input name="dp_minimum_persen" label="DP Minimum (%)" type="number" min="0" max="100" step="0.01" :value="$isEdit ? $unitRumah->dp_minimum_persen : null" />
    </div>
    @if($isEdit && $unitRumah->foto_unit)<div class="mb-4"><p class="mb-2 text-sm font-semibold text-gray-700">Foto saat ini</p><img src="{{ asset('storage/' . $unitRumah->foto_unit) }}" alt="{{ $unitRumah->kode_unit }}" class="h-28 w-40 rounded-xl object-cover"></div>@endif
    <x-form-file name="foto_unit" label="Foto Unit" accept="image/jpeg,image/png" />
    @if($isEdit && $unitRumah->denah_unit)<div class="mb-4"><p class="mb-2 text-sm font-semibold text-gray-700">Denah saat ini</p><a class="text-sm font-semibold text-primary hover:underline" href="{{ asset('storage/' . $unitRumah->denah_unit) }}" target="_blank" rel="noopener">Lihat denah</a></div>@endif
    <x-form-file name="denah_unit" label="Denah Unit" accept="image/jpeg,image/png,application/pdf" />
</fieldset>
