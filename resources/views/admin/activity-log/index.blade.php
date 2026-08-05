@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Activity Log</h1>
                <p class="mt-2 text-sm text-gray-600">Audit trail read-only untuk semua aktivitas user.</p>
            </div>
        </div>

        <x-card title="Filter">
            <form method="GET" action="{{ route('admin.activity-log.index') }}" class="grid gap-4 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">User</label>
                    <select name="user"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $filterUser == $user->id ? 'selected' : '' }}>
                                {{ $user->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Aksi</label>
                    <input type="text" name="aksi" value="{{ $filterAksi }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="create, update, delete, login" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Entitas</label>
                    <input type="text" name="entitas" value="{{ $filterEntitas }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="booking, konsumen, unit" />
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Periode Mulai</label>
                        <input type="date" name="periode_mulai" value="{{ $periodeMulai }}"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Periode Selesai</label>
                        <input type="date" name="periode_selesai" value="{{ $periodeSelesai }}"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary" />
                    </div>
                </div>

                <div class="flex items-end lg:col-span-4">
                    <button type="submit"
                        class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">Terapkan
                        Filter</button>
                </div>
            </form>
        </x-card>

        <x-card title="Audit Trail">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Entitas</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($activityLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-700">{{ $log->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $log->user?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ ucfirst(str_replace('_', ' ', $log->aksi)) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ ucfirst(str_replace('_', ' ', $log->entitas)) }}
                                    {{ $log->entitas_id ? "(#{$log->entitas_id})" : '' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $log->deskripsi ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $activityLogs->links() }}</div>
        </x-card>
    </div>
@endsection
