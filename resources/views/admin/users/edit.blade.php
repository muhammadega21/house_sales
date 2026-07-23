@extends('layouts.app')

@section('content')
@php($isCurrentUser = auth()->id() === $user->id)
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1><p class="mt-1 text-sm text-gray-500">Perbarui data {{ $user->nama_lengkap }}.</p></div>
        <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
    </div>

    <x-card title="Informasi Pengguna" subtitle="Kosongkan password apabila tidak ingin menggantinya.">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid gap-x-6 md:grid-cols-2">
                <x-form-input name="nama_lengkap" label="Nama Lengkap" :value="$user->nama_lengkap" :required="true" maxlength="100" autocomplete="name" />
                <x-form-input name="username" label="Username" :value="$user->username" :required="true" maxlength="50" autocomplete="username" />
                <x-form-input name="password" label="Password Baru" type="password" placeholder="Kosongkan jika tidak diubah" minlength="8" autocomplete="new-password" />
                <x-form-input name="password_confirmation" label="Konfirmasi Password Baru" type="password" placeholder="Ulangi password baru" minlength="8" autocomplete="new-password" />
                <x-form-input name="email" label="Email" type="email" :value="$user->email" maxlength="100" autocomplete="email" />
                <x-form-input name="no_hp" label="No. HP" type="tel" :value="$user->no_hp" maxlength="15" autocomplete="tel" />
                @if($isCurrentUser)
                    <input type="hidden" name="role" value="{{ $user->role->value }}">
                    <x-form-select name="role_display" label="Role" :options="[$user->role->value => $user->role->label()]" :selected="$user->role->value" disabled />
                @else
                    <x-form-select name="role" label="Role" :options="collect($roles)->mapWithKeys(fn ($role) => [$role->value => $role->label()])->all()" :selected="$user->role->value" :required="true" />
                @endif
                <x-form-select name="status" label="Status" :options="['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif']" :selected="$user->status" :required="true" />
            </div>
            @if($user->foto_profil)
                <div class="mb-4 flex items-center gap-4"><img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama_lengkap }}" class="h-20 w-20 rounded-xl object-cover"><p class="text-sm text-gray-500">Foto profil saat ini. Upload foto baru untuk menggantinya.</p></div>
            @endif
            <x-form-file name="foto_profil" label="Foto Profil Baru" accept="image/jpeg,image/png" />
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark">Perbarui</button>
            </div>
        </form>
    </x-card>
</div>
@endsection