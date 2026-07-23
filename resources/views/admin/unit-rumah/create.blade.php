@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><div><h1 class="text-2xl font-bold text-gray-900">Tambah Unit Rumah</h1><p class="mt-1 text-sm text-gray-500">Tambahkan unit rumah pada perumahan aktif.</p></div><a href="{{ route('admin.unit-rumah.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a></div>
    <x-card title="Informasi Unit Rumah" subtitle="Kolom bertanda * wajib diisi."><form method="POST" action="{{ route('admin.unit-rumah.store') }}" enctype="multipart/form-data">@csrf @include('admin.unit-rumah._form')<div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6"><a href="{{ route('admin.unit-rumah.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a><button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">Simpan</button></div></form></x-card>
</div>
@endsection
