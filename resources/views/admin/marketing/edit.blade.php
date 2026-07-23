@extends('layouts.app')
@section('content')
<div class="space-y-6"><div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold">Edit Marketing</h1><p class="mt-1 text-sm text-gray-500">Password bersifat opsional.</p></div><a href="{{ route('admin.marketing.index') }}" class="rounded-lg border px-4 py-2.5 text-sm font-semibold">Kembali</a></div><x-card title="Data Marketing"><form method="POST" action="{{ route('admin.marketing.update', $marketing) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.marketing._form')<div class="mt-6 flex justify-end border-t pt-6"><button class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Perbarui</button></div></form></x-card></div>
@endsection
