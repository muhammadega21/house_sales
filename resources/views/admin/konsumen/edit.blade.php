@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Konsumen</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data konsumen #{{ $konsumen->id }}.</p>
        </div>
    </div>

    <x-card>
        <form action="{{ route('admin.konsumen.update', $konsumen->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">Data Pribadi</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_lengkap" label="Nama Lengkap" :value="old('nama_lengkap', $konsumen->nama_lengkap)" :required="true" maxlength="100" placeholder="Sesuai KTP" />
                <x-form-input name="nik" label="NIK / KTP" :value="old('nik', $konsumen->nik)" :required="true" maxlength="16" placeholder="16 digit angka" />
                <x-form-input name="no_kk" label="No. KK" :value="old('no_kk', $konsumen->no_kk)" maxlength="16" placeholder="opsional" />
                <x-form-input name="no_hp" label="No. HP" :value="old('no_hp', $konsumen->no_hp)" :required="true" maxlength="15" placeholder="0812xxxxxxxx" />
                <x-form-input name="email" label="Email" type="email" :value="old('email', $konsumen->email)" placeholder="opsional" />
                <x-form-textarea name="alamat_lengkap" label="Alamat Lengkap" :value="old('alamat_lengkap', $konsumen->alamat_lengkap)" :required="true" rows="2" placeholder="Alamat lengkap sesuai KTP" />
                <x-form-input name="tempat_lahir" label="Tempat Lahir" :value="old('tempat_lahir', $konsumen->tempat_lahir)" maxlength="50" placeholder="opsional" />
                <x-form-input name="tanggal_lahir" label="Tanggal Lahir" type="date" :value="old('tanggal_lahir', $konsumen->tanggal_lahir?->format('Y-m-d'))" />
                <x-form-select name="jenis_kelamin" label="Jenis Kelamin" :options="['L' => 'Laki-Laki', 'P' => 'Perempuan']" :selected="old('jenis_kelamin', $konsumen->jenis_kelamin?->value)" />
                <x-form-select name="status_pernikahan" label="Status Pernikahan" :options="['belum_menikah' => 'Belum Menikah', 'menikah' => 'Menikah', 'cerai_hidup' => 'Cerai Hidup', 'cerai_mati' => 'Cerai Mati']" :selected="old('status_pernikahan', $konsumen->status_pernikahan?->value)" />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4 mt-6">Data Pekerjaan</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="pekerjaan" label="Pekerjaan" :value="old('pekerjaan', $konsumen->pekerjaan)" maxlength="100" placeholder="opsional" />
                <x-form-input name="nama_perusahaan" label="Nama Perusahaan" :value="old('nama_perusahaan', $konsumen->nama_perusahaan)" maxlength="100" placeholder="opsional" />
                <x-form-input name="penghasilan_bulanan" label="Penghasilan Bulanan" type="number" min="0" step="0.01" :value="old('penghasilan_bulanan', $konsumen->penghasilan_bulanan)" placeholder="opsional" />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4 mt-6">Data Tambahan</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="npwp" label="NPWP" :value="old('npwp', $konsumen->npwp)" maxlength="15" placeholder="opsional" />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4 mt-6">Dokumen</h2>
            <div class="grid gap-x-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto KTP Saat Ini</label>
                    @if($konsumen->foto_ktp)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('storage/' . $konsumen->foto_ktp) }}" alt="Foto KTP" class="h-20 w-14 rounded-lg object-cover border border-gray-200">
                            <span class="text-xs text-gray-500">Klik upload baru untuk mengganti</span>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mb-2">Tidak ada foto KTP</p>
                    @endif
                    <x-form-file name="foto_ktp" label="Foto KTP (ganti jika perlu)" accept="image/*" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto KK Saat Ini</label>
                    @if($konsumen->foto_kk)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('storage/' . $konsumen->foto_kk) }}" alt="Foto KK" class="h-20 w-14 rounded-lg object-cover border border-gray-200">
                            <span class="text-xs text-gray-500">Klik upload baru untuk mengganti</span>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mb-2">Tidak ada foto KK</p>
                    @endif
                    <x-form-file name="foto_kk" label="Foto KK (ganti jika perlu)" accept="image/*" />
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ route('admin.konsumen.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection