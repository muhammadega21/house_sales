@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Pengguna</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola akun internal Admin, Marketing, dan Manajemen.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
            <span aria-hidden="true">+</span>
            Tambah Pengguna
        </a>
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <x-card><p class="text-sm text-gray-500">Total Admin</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalAdmin }}</p></x-card>
        <x-card><p class="text-sm text-gray-500">Total Marketing</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalMarketing }}</p></x-card>
        <x-card><p class="text-sm text-gray-500">Total Manajemen</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalManajemen }}</p></x-card>
    </div>

    <x-card>
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-4 md:grid-cols-4 md:items-end">
            <x-form-input name="search" label="Cari Pengguna" placeholder="Nama, username, atau role" :value="request('search')" />
            <x-form-select name="role" label="Role" :options="['admin' => 'Admin', 'marketing' => 'Marketing', 'manajemen' => 'Manajemen']" :selected="request('role')" />
            <x-form-select name="status" label="Status" :options="['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif']" :selected="request('status')" />
            <div class="mb-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark">Terapkan</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
                @endif
            </div>
        </form>
    </x-card>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">No. HP</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-4 py-4 text-gray-600">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->foto_profil)
                                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama_lengkap }}" class="h-9 w-9 rounded-full object-cover">
                                    @else
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary">{{ strtoupper(mb_substr($user->nama_lengkap, 0, 1)) }}</span>
                                    @endif
                                    <div><p class="font-semibold text-gray-900">{{ $user->nama_lengkap }}</p><p class="text-xs text-gray-500">{{ $user->email ?: '-' }}</p></div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-700">{{ $user->username }}</td>
                            <td class="px-4 py-4"><x-badge :status="$user->role->value" /></td>
                            <td class="px-4 py-4 text-gray-700">{{ $user->no_hp ?: '-' }}</td>
                            <td class="px-4 py-4"><x-badge :status="$user->status" /></td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-primary transition hover:text-primary-dark">Edit</a>
                                    @if(auth()->id() !== $user->id)
                                        <x-confirm-delete :route="route('admin.users.destroy', $user)" :item-name="$user->nama_lengkap" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Data pengguna tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer><x-pagination :paginator="$users" /></x-slot:footer>
    </x-card>
</div>
@endsection