@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Pengguna</h1>
                <p class="mt-1 text-sm text-gray-500">Buat akun pengguna internal baru.</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
        </div>

        <x-card title="Informasi Pengguna" subtitle="Kolom bertanda * wajib diisi.">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-x-6 md:grid-cols-2">
                    <x-form-input name="nama_lengkap" label="Nama Lengkap" placeholder="Masukkan nama lengkap"
                        :required="true" maxlength="100" autocomplete="name" />
                    <x-form-input name="username" label="Username" placeholder="Masukkan username" :required="true"
                        maxlength="50" autocomplete="username" />
                    <x-form-input name="password" label="Password" type="password" placeholder="Minimal 8 karakter"
                        :required="true" minlength="8" autocomplete="new-password" />
                    <x-form-input name="password_confirmation" label="Konfirmasi Password" type="password"
                        placeholder="Ulangi password" :required="true" minlength="8" autocomplete="new-password" />
                    <x-form-input name="email" label="Email" type="email" placeholder="email@contoh.com"
                        maxlength="100" autocomplete="email" />
                    <x-form-input name="no_hp" label="No. HP" type="tel" placeholder="08xxxxxxxxxx" maxlength="15"
                        autocomplete="tel" />
                    <x-form-select name="role" label="Role" :options="collect($roles)->mapWithKeys(fn($role) => [$role->value => $role->label()])->all()" :required="true" />
                    <x-form-select name="status" label="Status" :options="['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif']" selected="aktif" :required="true" />
                </div>
                <x-form-file name="foto_profil" label="Foto Profil" accept="image/jpeg,image/png,image/webp" />
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.users.index') }}"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
                    <button type="submit"
                        class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark">Simpan</button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
