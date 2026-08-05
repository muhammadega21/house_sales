@extends('layouts.app')

@section('title', 'Detail Booking - ' . $booking->kode_booking)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $booking->kode_booking }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <x-badge :status="$booking->statusPenjualan?->status_saat_ini?->value ?? 'booking'" />
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $booking->status_pembayaran_fee->color() }}-100 text-{{ $booking->status_pembayaran_fee->color() }}-800">
                        {{ $booking->status_pembayaran_fee->label() }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $booking->tanggal_booking->format('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.booking.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('admin.booking.tracking', $booking->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 shadow-sm transition hover:bg-amber-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                    Lacak Status
                </a>
            </div>
        </div>

        {{-- 2 Kolom Layout --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Kolom Kiri: Info Utama --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informasi Konsumen --}}
                <x-card title="Informasi Konsumen">
                    <div class="flex items-start justify-between">
                        <x-info-konsumen-card :konsumen="$booking->konsumen" />
                        <a href="{{ route('admin.konsumen.show', $booking->konsumen?->id) }}"
                            class="shrink-0 text-xs font-medium text-primary hover:text-primary-dark">
                            Lihat Detail →
                        </a>
                    </div>
                </x-card>

                {{-- Informasi Unit --}}
                <x-card title="Informasi Unit">
                    <div class="flex items-start justify-between">
                        <x-info-unit-card :unit="$booking->unit" />
                        <a href="{{ route('admin.unit-rumah.show', $booking->unit?->id) }}"
                            class="shrink-0 text-xs font-medium text-primary hover:text-primary-dark">
                            Lihat Detail →
                        </a>
                    </div>
                </x-card>

                {{-- Informasi Marketing PIC --}}
                <x-card title="Marketing PIC">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $booking->marketing?->nama_lengkap ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $booking->marketing?->no_hp ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                {{-- Riwayat Pembayaran --}}
                <x-card title="Riwayat Pembayaran">
                    <x-progress-pembayaran :total-terbayar="$totalTerverifikasi" :harga-unit="$booking->unit?->harga_jual ?? 0" />

                    <div class="mt-4">
                        @include('marketing.booking.riwayat-pembayaran', [
                            'pembayaran' => $booking->pembayaran,
                        ])
                    </div>

                    @if ($sisaTagihan > 0)
                        <div
                            class="mt-4 flex items-center justify-between rounded-lg bg-amber-50 border border-amber-200 p-3">
                            <div class="text-sm">
                                <span class="text-gray-600">Sisa Tagihan:</span>
                                <span class="ml-2 font-semibold text-gray-900">Rp
                                    {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('marketing.pembayaran.create', ['idBooking' => $booking->id]) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Input Pembayaran
                            </a>
                        </div>
                    @endif

                    {{-- Tombol Verifikasi Pembayaran (jika ada pending) --}}
                    @php
                        $pembayaranPending = $booking->pembayaran->where('status_verifikasi', 'pending');
                    @endphp
                    @if ($pembayaranPending->isNotEmpty())
                        <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 p-3">
                            <p class="text-sm font-semibold text-amber-800">Pembayaran Pending Verifikasi</p>
                            <div class="mt-2 space-y-2">
                                @foreach ($pembayaranPending as $p)
                                    <div
                                        class="flex items-center justify-between rounded bg-white p-2 border border-amber-100">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ $p->jenis_pembayaran->label() }}</span>
                                            <span class="text-gray-500 ml-2">Rp
                                                {{ number_format($p->nominal, 0, ',', '.') }}</span>
                                        </div>
                                        <a href="{{ route('admin.pembayaran.verifikasi', $p->id) }}"
                                            class="inline-flex items-center gap-1 rounded bg-primary px-2 py-1 text-xs font-semibold text-white hover:bg-primary-dark">
                                            Verifikasi
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-card>

                {{-- Timeline Status --}}
                <x-card title="Timeline Status">
                    <x-status-timeline :histories="$booking->statusHistory" :current-status="$booking->statusPenjualan?->status_saat_ini?->value" />
                </x-card>

                {{-- Catatan --}}
                <x-card title="Catatan">
                    @if ($booking->catatan)
                        <p class="text-sm text-gray-700">{{ $booking->catatan }}</p>
                    @else
                        <p class="text-sm text-gray-400">Tidak ada catatan</p>
                    @endif

                    @php
                        $catatanVerifikasi = $booking->pembayaran
                            ->where('status_verifikasi', 'diverifikasi')
                            ->whereNotNull('catatan_verifikasi')
                            ->where('catatan_verifikasi', '!=');
                    @endphp

                    @if ($catatanVerifikasi->isNotEmpty())
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Catatan Verifikasi
                            </h4>
                            @foreach ($catatanVerifikasi as $p)
                                <div class="mb-2 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm">
                                    <p class="font-medium text-emerald-800">{{ $p->jenis_pembayaran->label() }} -
                                        {{ $p->tanggal_bayar->format('d/m/Y') }}</p>
                                    <p class="text-emerald-700">{{ $p->catatan_verifikasi }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                {{-- Log Aktivitas --}}
                <x-card title="Log Aktivitas">
                    @if ($booking->statusHistory->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead
                                    class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Oleh</th>
                                        <th class="px-4 py-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($booking->statusHistory->sortByDesc('created_at') as $history)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                                {{ $history->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <x-badge :status="$history->status_sesudah" />
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ $history->user?->nama_lengkap ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $history->catatan ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Tidak ada log aktivitas</p>
                    @endif
                </x-card>
            </div>

            {{-- Kolom Kanan: Sidebar Aksi --}}
            <div class="space-y-6">
                {{-- Progress Bar --}}
                <x-card>
                    <x-progress-pembayaran :total-terbayar="$totalTerverifikasi" :harga-unit="$booking->unit?->harga_jual ?? 0" />
                </x-card>

                {{-- Aksi --}}
                <x-card title="Aksi">
                    @php
                        $statusSekarang = $booking->statusPenjualan?->status_saat_ini?->value;
                    @endphp

                    @if ($statusSekarang === 'booking')
                        <div class="space-y-3">
                            <a href="{{ route('marketing.pembayaran.create', ['idBooking' => $booking->id]) }}"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Input Pembayaran
                            </a>

                            @php
                                $docsLengkap =
                                    $booking->konsumen?->dokumenKpr->where('status_verifikasi', 'valid')->count() ===
                                        $booking->konsumen?->dokumenKpr->count() &&
                                    $booking->konsumen?->dokumenKpr->isNotEmpty();
                            @endphp
                            <a href="{{ route('admin.pengajuan-kpr.show', $booking->pengajuanKpr?->id ?? '#') }}"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 {{ $docsLengkap ? '' : 'opacity-50 cursor-not-allowed pointer-events-none' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Ajukan KPR
                            </a>
                            @if (!$docsLengkap)
                                <p class="text-xs text-amber-600">Dokumen KPR belum lengkap</p>
                            @endif

                            <a href="{{ route('admin.booking.edit', $booking->id) }}"
                                class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Booking
                            </a>

                            <a href="{{ route('admin.booking.cancel', $booking->id) }}"
                                class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batalkan Booking
                            </a>
                        </div>
                    @elseif($statusSekarang === 'pengajuan_kpr')
                        <div class="space-y-3">
                            @if ($booking->pengajuanKpr)
                                <div class="rounded-lg bg-indigo-50 border border-indigo-200 p-4">
                                    <p class="text-sm font-semibold text-indigo-900">Pengajuan KPR</p>
                                    <div class="mt-2 space-y-1 text-sm text-indigo-700">
                                        <p>Nama Bank: {{ $booking->pengajuanKpr->nama_bank ?? '-' }}</p>
                                        <p>Plafon: Rp
                                            {{ number_format($booking->pengajuanKpr->plafon_kpr ?? 0, 0, ',', '.') }}</p>
                                        <p>Tenor: {{ $booking->pengajuanKpr->tenor_tahun ?? '-' }} tahun</p>
                                        <p>Suku Bunga: {{ $booking->pengajuanKpr->suku_bunga ?? '-' }}%</p>
                                        <p>Status: <x-badge :status="$booking->pengajuanKpr->status_pengajuan" /></p>
                                        <p>Tanggal Pengajuan:
                                            {{ $booking->pengajuanKpr->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</p>
                                    </div>
                                    <a href="{{ route('admin.pengajuan-kpr.show', $booking->pengajuanKpr->id) }}"
                                        class="mt-3 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                        Lihat Detail →
                                    </a>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Belum ada pengajuan KPR</p>
                            @endif
                        </div>
                    @elseif($statusSekarang === 'akad')
                        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                            <p class="text-sm font-semibold text-blue-900">Status: Akad</p>
                            <p class="mt-1 text-sm text-blue-700">Booking telah mencapai tahap akad. Proses penandatanganan
                                akad telah selesai.</p>
                            @if ($booking->pengajuanKpr)
                                <div class="mt-2 text-sm text-blue-600">
                                    <p>Bank: {{ $booking->pengajuanKpr->nama_bank ?? '-' }}</p>
                                    <p>Plafon: Rp {{ number_format($booking->pengajuanKpr->plafon_kpr ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @elseif($statusSekarang === 'serah_terima')
                        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4">
                            <p class="text-sm font-semibold text-emerald-900">Status: Serah Terima</p>
                            <p class="mt-1 text-sm text-emerald-700">Unit telah diserahkan ke konsumen. Proses booking
                                telah selesai.</p>
                        </div>
                    @elseif($statusSekarang === 'batal')
                        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm font-semibold text-red-800">Booking Dibatalkan</p>
                            </div>
                            @if ($booking->statusPenjualan?->catatan)
                                <p class="mt-2 text-sm text-red-700">Alasan: {{ $booking->statusPenjualan->catatan }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Tombol Ubah Status Penjualan (Admin only) --}}
                    @if (in_array($statusSekarang, ['prospek', 'booking', 'pengajuan_kpr', 'akad']))
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Ubah Status
                                Penjualan</p>
                            <form action="{{ route('admin.status-penjualan.update', $booking->statusPenjualan?->id) }}"
                                method="POST" id="formUbahStatus">
                                @csrf @method('PUT')
                                <select name="status_baru" id="statusBaru"
                                    class="w-full rounded-lg border border-gray-300 py-2 text-sm transition focus:border-primary focus:ring-primary"
                                    onchange="this.closest('form').submit()">
                                    <option value="">-- Pilih Status --</option>
                                    @if ($statusSekarang === 'prospek')
                                        <option value="booking">Booking</option>
                                        <option value="batal">Batal</option>
                                    @elseif($statusSekarang === 'booking')
                                        <option value="pengajuan_kpr">Pengajuan KPR</option>
                                        <option value="batal">Batal</option>
                                    @elseif($statusSekarang === 'pengajuan_kpr')
                                        <option value="akad">Akad</option>
                                        <option value="batal">Batal</option>
                                    @elseif($statusSekarang === 'akad')
                                        <option value="serah_terima">Serah Terima</option>
                                        <option value="batal">Batal</option>
                                    @endif
                                </select>
                            </form>
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Ubah Status --}}
    <div id="modalUbahStatus" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-ubah-status-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="document.getElementById('modalUbahStatus').classList.add('hidden')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold text-gray-900" id="modal-ubah-status-title">Konfirmasi
                                    Ubah Status</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin mengubah status penjualan?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" form="formUbahStatus"
                            class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark sm:ml-3 sm:w-auto">
                            Ya, Ubah Status
                        </button>
                        <button type="button"
                            onclick="document.getElementById('modalUbahStatus').classList.add('hidden')"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
