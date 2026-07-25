@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Konsumen</h1>
            <p class="mt-1 text-sm text-gray-500">Input data konsumen baru.</p>
        </div>
    </div>

    <x-card>
        <form action="{{ route('marketing.konsumen.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">Data Pribadi</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_lengkap" label="Nama Lengkap" :required="true" maxlength="100" placeholder="Sesuai KTP" />
                <x-form-input name="nik" label="NIK / KTP" :required="true" maxlength="16" placeholder="16 digit angka" />
                <x-form-input name="no_kk" label="No. KK" maxlength="16" placeholder="opsional" />
                <x-form-input name="no_hp" label="No. HP" :required="true" maxlength="15" placeholder="0812xxxxxxxx" />
                <x-form-input name="email" label="Email" type="email" placeholder="opsional" />
                <x-form-textarea name="alamat_lengkap" label="Alamat Lengkap" :required="true" rows="2" placeholder="Alamat lengkap sesuai KTP" />
                <x-form-input name="tempat_lahir" label="Tempat Lahir" maxlength="50" placeholder="opsional" />
                <x-form-input name="tanggal_lahir" label="Tanggal Lahir" type="date" />
                <x-form-select name="jenis_kelamin" label="Jenis Kelamin" :options="['L' => 'Laki-Laki', 'P' => 'Perempuan']" />
                <x-form-select name="status_pernikahan" label="Status Pernikahan" :options="['belum_menikah' => 'Belum Menikah', 'menikah' => 'Menikah', 'cerai_hidup' => 'Cerai Hidup', 'cerai_mati' => 'Cerai Mati']" />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4 mt-6">Data Pekerjaan</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="pekerjaan" label="Pekerjaan" maxlength="100" placeholder="opsional" />
                <x-form-input name="nama_perusahaan" label="Nama Perusahaan" maxlength="100" placeholder="opsional" />
                <x-form-input name="penghasilan_bulanan" label="Penghasilan Bulanan" type="number" min="0" step="0.01" placeholder="opsional" />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4 mt-6">Data Tambahan</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="npwp" label="NPWP" maxlength="15" placeholder="opsional" />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4 mt-6">Dokumen</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-file name="foto_ktp" label="Foto KTP" accept="image/*" />
                <x-form-file name="foto_kk" label="Foto KK" accept="image/*" />
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('marketing.konsumen.index') }}"
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