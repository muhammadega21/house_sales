@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Pengguna</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola akun internal Admin, Marketing, dan Manajemen.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Pengguna
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid gap-5 sm:grid-cols-3">
        <x-card><p class="text-sm text-gray-500">Total Admin</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalAdmin }}</p></x-card>
        <x-card><p class="text-sm text-gray-500">Total Marketing</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalMarketing }}</p></x-card>
        <x-card><p class="text-sm text-gray-500">Total Manajemen</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalManajemen }}</p></x-card>
    </div>

    {{-- Data Table Toolbar --}}
    <x-data-table-toolbar
        search-placeholder="Cari nama, username, email, atau role..."
        :search-route="route('admin.users.index')"
        :per-page="$perPage"
        :total="$totalAll"
        :filtered="$users->total()"
        :search="$search"
        :has-filters="$hasFilters"
        :exclude-keys="['role', 'status']"
    >
        <x-slot:filters>
            <div class="min-w-[160px]">
                <label for="role" class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="role" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="marketing" {{ request('role') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="manajemen" {{ request('role') === 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non_aktif" {{ request('status') === 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
        </x-slot:filters>
    </x-data-table-toolbar>

    {{-- Data Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 w-12">No</th>
                        <x-sort-header column="nama_lengkap" label="Nama" :sort-by="$sortBy" :sort-dir="$sortDir" />
                        <x-sort-header column="username" label="Username" :sort-by="$sortBy" :sort-dir="$sortDir" />
                        <x-sort-header column="role" label="Role" :sort-by="$sortBy" :sort-dir="$sortDir" />
                        <th class="px-4 py-3">No. HP</th>
                        <x-sort-header column="status" label="Status" :sort-by="$sortBy" :sort-dir="$sortDir" />
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
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $user->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email ?: '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-700">{{ $user->username }}</td>
                            <td class="px-4 py-4"><x-badge :status="$user->role->value" /></td>
                            <td class="px-4 py-4 text-gray-700">{{ $user->no_hp ?: '-' }}</td>
                            <td class="px-4 py-4"><x-badge :status="$user->status" /></td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-primary transition hover:text-primary-dark" title="Edit">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <x-confirm-delete :route="route('admin.users.destroy', $user)" :item-name="$user->nama_lengkap" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state
                                    title="Belum ada data"
                                    message="Silakan tambahkan pengguna baru atau sesuaikan filter pencarian Anda."
                                    :search="$search"
                                    :create-route="route('admin.users.create')"
                                    create-label="Tambah Pengguna"
                                    :reset-route="route('admin.users.index')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>
            <x-pagination :paginator="$users" />
        </x-slot:footer>
    </x-card>
</div>
@endsection
