@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
                @if($konsumen->foto_ktp)
                    <img src="{{ asset('storage/' . $konsumen->foto_ktp) }}" alt="Foto KTP" class="h-full w-full object-cover">
                @else
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $konsumen->nama_lengkap }}</h1>
                <p class="text-sm text-gray-500">NIK: {{ $konsumen->nik }} &nbsp;|&nbsp; HP: {{ $konsumen->no_hp }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.konsumen.edit', $konsumen->id) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a href="{{ route('marketing.konsumen.index') }}?id_konsumen={{ $konsumen->id }}"
               class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                Booking Rumah
            </a>
            <a href="{{ route('admin.konsumen.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-card>
            <p class="text-sm text-gray-500">Marketing PIC</p>
            <p class="mt-1 font-semibold text-gray-900">{{ $konsumen->marketing?->nama_lengkap ?? '-' }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Tanggal Terdaftar</p>
            <p class="mt-1 font-semibold text-gray-900">{{ $konsumen->created_at?->format('d/m/Y') }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Jumlah Booking</p>
            <p class="mt-1 font-semibold text-gray-900">{{ $konsumen->bookings->count() }}</p>
        </x-card>
    </div>

    {{-- Prospek Asal (if exists) --}}
    @if($konsumen->prospek)
        <x-card>
            <div class="flex items-center gap-2 mb-2">
                <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">Prospek Asal</h3>
            </div>
            <div class="grid gap-x-6 md:grid-cols-3 text-sm">
                <div><span class="text-gray-400">Nama:</span> {{ $konsumen->prospek->nama_prospek }}</div>
                <div><span class="text-gray-400">No HP:</span> {{ $konsumen->prospek->no_hp }}</div>
                <div><span class="text-gray-400">Sumber:</span> {{ $konsumen->prospek->sumber_prospek?->label() ?? '-' }}</div>
            </div>
        </x-card>
    @endif

    <x-card>
        <div class="border-b border-gray-200">
            <nav class="flex gap-6 px-6" id="tabs">
                @php $activeTab = request('tab', 'data'); @endphp
                <a href="?tab=data" class="pb-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'data' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Data Pribadi</a>
                <a href="?tab=dokumen" class="pb-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'dokumen' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Dokumen</a>
                <a href="?tab=booking" class="pb-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'booking' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Riwayat Booking</a>
                <a href="?tab=dokumen-kpr" class="pb-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'dokumen-kpr' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Dokumen KPR</a>
                <a href="?tab=pengajuan-kpr" class="pb-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'pengajuan-kpr' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Pengajuan KPR</a>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'data')
                <div class="grid gap-x-6 md:grid-cols-2">
                    <div><p class="text-xs text-gray-400 uppercase">Nama Lengkap</p><p class="font-semibold">{{ $konsumen->nama_lengkap }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">NIK</p><p class="font-semibold font-mono">{{ $konsumen->nik }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">No. KK</p><p class="font-semibold">{{ $konsumen->no_kk ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">No. HP</p><p class="font-semibold">{{ $konsumen->no_hp }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Email</p><p class="font-semibold">{{ $konsumen->email ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Alamat</p><p class="font-semibold">{{ $konsumen->alamat_lengkap }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Tempat Lahir</p><p class="font-semibold">{{ $konsumen->tempat_lahir ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Tanggal Lahir</p><p class="font-semibold">{{ $konsumen->tanggal_lahir?->format('d/m/Y') ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Jenis Kelamin</p><p class="font-semibold">{{ $konsumen->jenis_kelamin?->label() ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Status Pernikahan</p><p class="font-semibold">{{ $konsumen->status_pernikahan?->label() ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Pekerjaan</p><p class="font-semibold">{{ $konsumen->pekerjaan ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Nama Perusahaan</p><p class="font-semibold">{{ $konsumen->nama_perusahaan ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">Penghasilan Bulanan</p><p class="font-semibold">{{ $konsumen->penghasilan_bulanan ? 'Rp ' . number_format($konsumen->penghasilan_bulanan, 0, ',', '.') : '-' }}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase">NPWP</p><p class="font-semibold">{{ $konsumen->npwp ?? '-' }}</p></div>
                </div>
            @elseif($activeTab === 'dokumen')
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Foto KTP</h3>
                        @if($konsumen->foto_ktp)
                            <a href="{{ asset('storage/' . $konsumen->foto_ktp) }}" target="_blank">
                                <img src="{{ asset('storage/' . $konsumen->foto_ktp) }}" alt="KTP" class="rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer max-h-64 object-contain">
                            </a>
                        @else
                            <p class="text-sm text-gray-400">Tidak ada foto KTP</p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Foto KK</h3>
                        @if($konsumen->foto_kk)
                            <a href="{{ asset('storage/' . $konsumen->foto_kk) }}" target="_blank">
                                <img src="{{ asset('storage/' . $konsumen->foto_kk) }}" alt="KK" class="rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer max-h-64 object-contain">
                            </a>
                        @else
                            <p class="text-sm text-gray-400">Tidak ada foto KK</p>
                        @endif
                    </div>
                </div>
            @elseif($activeTab === 'booking')
                @if($konsumen->bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Unit</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Status Bayar</th>
                                    <th class="px-4 py-3">Booking Fee</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($konsumen->bookings as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-xs">{{ $booking->unit?->kode_unit ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $booking->tanggal_booking?->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $ps = $booking->status_pembayaran_fee;
                                                $pc = match($ps) {
                                                    'sudah_bayar' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'refund' => 'bg-red-100 text-red-800 border-red-200',
                                                    default => 'bg-amber-100 text-amber-800 border-amber-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border {{ $pc }}">{{ __($ps) }}</span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold">Rp {{ number_format($booking->booking_fee, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400">Belum ada riwayat booking</p>
                @endif
            @elseif($activeTab === 'dokumen-kpr')
                @if($konsumen->dokumenKpr->count() > 0)
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($konsumen->dokumenKpr as $dokumen)
                            <div class="rounded-lg border border-gray-200 bg-white p-3">
                                <p class="text-xs font-semibold text-gray-900 mb-1">{{ $dokumen->jenis_dokumen ?? 'Lainnya' }}</p>
                                <p class="text-xs text-gray-500 mb-2">{{ $dokumen->nama_file }}</p>
                                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border {{ match($dokumen->status_verifikasi) { 'valid' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'tidak_valid' => 'bg-red-100 text-red-800 border-red-200', 'perlu_revisi' => 'bg-amber-100 text-amber-800 border-amber-200', default => 'bg-gray-100 text-gray-800 border-gray-200' } }">
                                    {{ match($dokumen->status_verifikasi) { 'valid' => 'Valid', 'tidak_valid' => 'Tidak Valid', 'perlu_revisi' => 'Perlu Revisi', default => 'Belum Diverifikasi' } }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">Belum ada dokumen KPR</p>
                @endif
            @elseif($activeTab === 'pengajuan-kpr')
                @if($konsumen->pengajuanKpr->count() > 0)
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($konsumen->pengajuanKpr as $pengajuan)
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-semibold text-gray-900">{{ $pengajuan->nama_bank ?? '-' }}</p>
                                    <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border {{ match($pengajuan->status_pengajuan) { 'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'ditolak' => 'bg-red-100 text-red-800 border-red-200', default => 'bg-amber-100 text-amber-800 border-amber-200' } }}">
                                        {{ match($pengajuan->status_pengajuan) { 'draft' => 'Draft', 'diajukan' => 'Diajukan', 'verifikasi_bank' => 'Verifikasi Bank', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'akad' => 'Akad', 'batal' => 'Batal', default => $pengajuan->status_pengajuan } }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 space-y-1">
                                    <p>Plafon: Rp {{ number_format($pengajuan->plafon_kpr ?? 0, 0, ',', '.') }}</p>
                                    <p>Tenor: {{ $pengajuan->tenor_tahun ?? '-' }} tahun</p>
                                    <p>Tanggal: {{ $pengajuan->tanggal_pengajuan?->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">Belum ada pengajuan KPR</p>
                @endif
            @endif
        </div>
    </x-card>

    {{-- Total Nilai Transaksi --}}
    @php
        $totalTransaksi = $konsumen->bookings->sum('booking_fee');
    @endphp
    <x-card>
        <p class="text-sm text-gray-500">Total Nilai Transaksi (Booking Fee)</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</p>
    </x-card>
</div>
@endsection