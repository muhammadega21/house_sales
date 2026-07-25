@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Konversi Prospek menjadi Konsumen</h1>
            <p class="mt-1 text-sm text-gray-500">Ubah status prospek menjadi konsumen dan lengkapi data pembeli.</p>
        </div>
    </div>

    <x-card>
        <form action="{{ route($routeName, $prospek->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Read-only Prospek Data --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-5">
                <h3 class="mb-3 text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Prospek</h3>
                <div class="grid gap-x-6 md:grid-cols-3">
                    <div>
                        <p class="text-xs text-gray-500">Nama Prospek</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $prospek->nama_prospek }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">No HP</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $prospek->no_hp }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Sumber</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            @php $sumber = $prospek->sumber_prospek; @endphp
                            {{ $sumber ? $sumber->icon() . ' ' . $sumber->label() : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <h3 class="mb-4 text-lg font-semibold text-gray-900">Data Konsumen</h3>

            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_lengkap" label="Nama Lengkap (Sesuai KTP)" :required="true" maxlength="100" :value="old('nama_lengkap', $prospek->nama_prospek)" />
                <x-form-input name="nik" label="NIK" :required="true" maxlength="16" placeholder="16 digit NIK" :value="old('nik')" />
                <x-form-input name="no_kk" label="No KK" maxlength="16" placeholder="16 digit (opsional)" :value="old('no_kk')" />
                <x-form-input name="no_hp" label="No HP" :required="true" maxlength="15" :value="old('no_hp', $prospek->no_hp)" />
                <x-form-input name="email" label="Email" type="email" :value="old('email', $prospek->email)" />
                <div class="md:col-span-2">
                    <x-form-textarea name="alamat_lengkap" label="Alamat Lengkap" :required="true" rows="3" placeholder="Alamat sesuai KTP" :value="old('alamat_lengkap')" />
                </div>
                <x-form-input name="tempat_lahir" label="Tempat Lahir" placeholder="Kota kelahiran" :value="old('tempat_lahir')" />
                <x-form-input name="tanggal_lahir" label="Tanggal Lahir" type="date" :value="old('tanggal_lahir')" />
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') === 'L' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700">Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') === 'P' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700">Perempuan</span>
                        </label>
                    </div>
                </div>
                <x-form-select name="status_pernikahan" label="Status Pernikahan" :options="[
                    '' => 'Pilih Status',
                    'belum_menikah' => 'Belum Menikah',
                    'menikah' => 'Menikah',
                    'cerai_hidup' => 'Cerai Hidup',
                    'cerai_mati' => 'Cerai Mati',
                ]" :selected="old('status_pernikahan')" />
                <x-form-input name="pekerjaan" label="Pekerjaan" placeholder="Contoh: Karyawan Swasta" :value="old('pekerjaan')" />
                <x-form-input name="nama_perusahaan" label="Nama Perusahaan" placeholder="Nama perusahaan tempat bekerja" :value="old('nama_perusahaan')" />
                <x-form-input name="penghasilan_bulanan" label="Penghasilan Bulanan (Rp)" type="number" min="0" step="1000" placeholder="0" :value="old('penghasilan_bulanan')" />
                <x-form-input name="npwp" label="NPWP" placeholder="16 digit NPWP (opsional)" :value="old('npwp')" />
                <x-form-file name="foto_ktp" label="Foto KTP" accept="image/jpeg,image/png,image/webp,application/pdf" />
                <x-form-file name="foto_kk" label="Foto KK" accept="image/jpeg,image/png,image/webp,application/pdf" />
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end mt-6">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Konversi & Simpan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
