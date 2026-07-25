@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Perumahan</h1>
                <p class="mt-1 text-sm text-gray-500">Masukkan informasi kawasan perumahan.</p>
            </div><a href="{{ route('admin.perumahan.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
        </div>
        <x-card title="Informasi Perumahan" subtitle="Kolom bertanda * wajib diisi.">
            <form method="POST" action="{{ route('admin.perumahan.store') }}" enctype="multipart/form-data">@csrf
                <div class="grid gap-x-6 md:grid-cols-2"><x-form-input name="nama_perumahan" label="Nama Perumahan"
                        :required="true" maxlength="150" placeholder="Nama kawasan perumahan" /><x-form-input
                        name="kota" label="Kota/Kabupaten" :required="true" maxlength="50" /><x-form-input
                        name="provinsi" label="Provinsi" :required="true" maxlength="50" /><x-form-input name="kode_pos"
                        label="Kode Pos" maxlength="10" inputmode="numeric" />
                    <div class="md:col-span-2"><x-form-textarea name="alamat" label="Alamat Lengkap" :required="true"
                            rows="3" /></div><x-form-input name="latitude" label="Latitude" type="number"
                        step="0.00000001" placeholder="Contoh: -2.990934" /><x-form-input name="longitude" label="Longitude"
                        type="number" step="0.00000001" placeholder="Contoh: 104.756554" />
                    <div class="md:col-span-2"><x-form-textarea name="deskripsi" label="Deskripsi" rows="4"
                            placeholder="Deskripsi singkat kawasan perumahan" /></div><x-form-select name="status"
                        label="Status" :options="['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif']" selected="aktif" :required="true" />
                </div>
                <x-form-file name="foto_kawasan" label="Foto Kawasan" accept="image/jpeg,image/png,image/webp" />
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6"><a
                        href="{{ route('admin.perumahan.index') }}"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a><button
                        type="submit"
                        class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark">Simpan</button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
