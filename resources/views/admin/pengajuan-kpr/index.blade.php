@extends('layouts.app')

@section('title', 'Pengajuan KPR - Admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengajuan KPR</h1>
                <p class="text-sm text-gray-500">Kelola semua pengajuan KPR.</p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <x-card>
            <form method="GET" action="{{ route('admin.pengajuan-kpr.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Nama konsumen, NIK, atau bank..."
                        class="w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                </div>
                <div class="min-w-[160px]">
                    <label for="status_pengajuan" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status_pengajuan" id="status_pengajuan" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        <option value="">Semua</option>
                        <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ $filterStatus === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="verifikasi_bank" {{ $filterStatus === 'verifikasi_bank' ? 'selected' : '' }}>Verifikasi Bank</option>
                        <option value="disetujui" {{ $filterStatus === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ $filterStatus === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="akad" {{ $filterStatus === 'akad' ? 'selected' : '' }}>Akad</option>
                        <option value="batal" {{ $filterStatus === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="min-w-[120px]">
                    <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per halaman</label>
                    <select name="per_page" id="per_page" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @foreach ([10, 25, 50] as $option)
                            <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>
                                {{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($search !== '' || $filterStatus !== '')
                    <div class="flex items-end">
                        <a href="{{ route('admin.pengajuan-kpr.index') }}"
                            class="text-sm text-red-600 hover:text-red-800 font-medium">Hapus Filter</a>
                    </div>
                @endif
            </form>
        </x-card>

        <!-- Data Table -->
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Konsumen</th>
                            <th class="px-4 py-3">NIK</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Bank</th>
                            <th class="px-4 py-3">Plafon</th>
                            <th class="px-4 py-3">Tenor</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($pengajuans as $pengajuan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $pengajuans->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $pengajuan->konsumen?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4 font-mono text-xs text-gray-500">{{ $pengajuan->konsumen?->nik ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $pengajuan->unit?->kode_unit ?? '-' }}
                                    <span class="text-xs text-gray-400">{{ $pengajuan->unit?->tipe_rumah ?? '' }}</span>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $pengajuan->nama_bank ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-700">Rp
                                    {{ number_format($pengajuan->plafon_kpr ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-gray-500">{{ $pengajuan->tenor_tahun ?? '-' }} tahun</td>
                                <td class="px-4 py-4">
                                    <x-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                                <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $pengajuan->tanggal_pengajuan?->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('admin.pengajuan-kpr.show', $pengajuan->id) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-gray-400">
                                    Belum ada pengajuan KPR
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pengajuans->links() }}
            </div>
        </x-card>
    </div>
@endsection