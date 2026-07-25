@php($isEdit = isset($marketing))
<div class="grid gap-x-6 md:grid-cols-2"><x-form-input name="nama_lengkap" label="Nama Lengkap" :value="$isEdit ? $marketing->nama_lengkap : null"
        :required="true" maxlength="100" /><x-form-input name="username" label="Username" :value="$isEdit ? $marketing->username : null"
        :required="true" maxlength="50" /><x-form-input name="password" label="Password" type="password" :required="!$isEdit"
        placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}" /><x-form-input
        name="no_hp" label="Nomor HP" :value="$isEdit ? $marketing->no_hp : null" :required="true" maxlength="15" /><x-form-input
        name="email" label="Email" type="email" :value="$isEdit ? $marketing->email : null" /><x-form-input name="persentase_komisi"
        label="Persentase Komisi (%)" type="number" min="0" max="100" step="0.01" :value="$isEdit ? $marketing->persentase_komisi : null"
        :required="true" /><x-form-select name="status" label="Status" :options="['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif']" :selected="$isEdit ? $marketing->status : 'aktif'"
        :required="true" /></div>
@if ($isEdit && $marketing->foto_profil)
    <div class="mb-4">
        <img src="{{ asset('storage/' . $marketing->foto_profil) }}" alt="{{ $marketing->nama_lengkap }}"
            class="mb-4 h-24 w-24 rounded-full object-cover">
        <div>
            <button type="button"
                onclick="if(confirm('Hapus foto profil?')){ this.closest('form').querySelector('input[name=remove_foto_profil]').value = '1'; this.closest('form').submit(); }"
                class="inline-flex items-center gap-2 rounded-lg border border-red-300 px-3 py-1 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus
                Foto</button>
        </div>
    </div>
    <input type="hidden" name="remove_foto_profil" value="">
@endif
<x-form-file name="foto_profil" label="Foto Profil" accept="image/jpeg,image/png,image/webp" />
