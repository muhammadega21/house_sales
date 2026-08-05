@extends('layouts.app')

@section('title', 'Detail Pengajuan KPR')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan KPR</h1>
                <p class="text-sm text-gray-500">Pengajuan untuk booking {{ $pengajuan->booking->kode_booking }}.</p>
                <p class="text-sm text-gray-500 mt-1">Konsumen: {{ $pengajuan->konsumen?->nama_lengkap ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('marketing.pengajuan-kpr.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Kembali
                </a>
                @if(in_array($pengajuan->status_pengajuan, ['draft', 'ditolak'], true))
                    <a href="{{ route('marketing.pengajuan-kpr.edit', $pengajuan->id) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                        Edit Pengajuan
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <div class="md:flex md:items-center md:justify-between md:gap-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Informasi Pengajuan</h2>
                            <p class="mt-1 text-sm text-gray-500">Semua detail proses KPR dari pengajuan hingga akad.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm">
                                <p class="text-xs text-gray-500">Status saat ini</p>
                                <div class="mt-2"><x-badge :status="$pengajuan->status_pengajuan" /></div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm">
                                <p class="text-xs text-gray-500">Tanggal pengajuan</p>
                                <p class="mt-2 font-semibold text-gray-900">{{ $pengajuan->tanggal_pengajuan?->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Bank</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->nama_bank ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Plafon KPR</p>
                            <p class="mt-1 text-gray-900">Rp {{ number_format($pengajuan->plafon_kpr ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tenor</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tenor_tahun ?? '-' }} tahun</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Suku Bunga</p>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->suku_bunga ?? '-' }}%</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $pengajuan->catatan ?? 'Tidak ada catatan.' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-kpr-progress :status="$pengajuan->status_pengajuan" />
                    </div>
                </x-card>

                <div class="grid gap-6 lg:grid-cols-2">
                    <x-card title="Informasi Konsumen">
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</p>
                                <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->nama_lengkap ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">NIK</p>
                                <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->nik ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">No HP</p>
                                <p class="mt-1 text-gray-900">{{ $pengajuan->konsumen?->no_hp ?? '-' }}</p>
                            </div>
                        </div>
                    </x-card>

                    <x-card title="Informasi Unit">
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Unit</p>
                                <p class="mt-1 text-gray-900">{{ $pengajuan->booking->unit?->kode_unit ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tipe</p>
                                <p class="mt-1 text-gray-900">{{ $pengajuan->booking->unit?->tipe_rumah ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Perumahan</p>
                                <p class="mt-1 text-gray-900">{{ $pengajuan->booking->unit?->perumahan?->nama_perumahan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Harga Unit</p>
                                <p class="mt-1 text-gray-900">Rp {{ number_format($pengajuan->booking->unit?->harga_jual ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <x-card title="Estimasi Cicilan">
                    <x-estimasi-cicilan :plafon="$pengajuan->plafon_kpr" :tenor="$pengajuan->tenor_tahun" :bunga="$pengajuan->suku_bunga" />
                </x-card>

                <x-card title="Checklist Dokumen">
                    <div class="space-y-3">
                        @foreach ($dokumenChecklist as $item)
                            @php
                                $statusLabel = match ($item['status_verifikasi']) {
                                    'valid' => 'Valid',
                                    'perlu_revisi' => 'Perlu Revisi',
                                    'belum_diverifikasi' => $item['uploaded'] ? 'Belum Diverifikasi' : 'Belum Upload',
                                    default => ucfirst(str_replace('_', ' ', $item['status_verifikasi'])),
                                };
                                $badgeColor = $item['is_valid'] ? 'emerald' : ($item['uploaded'] ? 'amber' : 'gray');
                            @endphp
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['wajib'] ? 'Wajib' : 'Opsional' }}</p>
                                </div>
                                <div class="inline-flex items-center gap-2 rounded-full bg-{{ $badgeColor }}-50 px-3 py-1 text-xs font-semibold text-{{ $badgeColor }}-700">
                                    {{ $statusLabel }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        <p>Dokumen lengkap jika semua dokumen wajib sudah diupload dan diverifikasi.</p>
                        <a href="{{ route('marketing.dokumen.index', ['id_konsumen' => $pengajuan->id_konsumen]) }}" class="text-primary hover:text-primary-dark">Kelola dokumen KPR</a>
                    </div>
                </x-card>

                <x-card title="Timeline Status">
                    <x-status-timeline :histories="$statusHistory" :current-status="$pengajuan->status_pengajuan" />
                </x-card>

                <x-card title="Aplikasi Lain Konsumen">
                    @if($otherPengajuans->isEmpty())
                        <p class="text-sm text-gray-400">Tidak ada aplikasi KPR lain untuk konsumen ini.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($otherPengajuans as $other)
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm">
                                    <p class="font-semibold text-gray-900">{{ $other->nama_bank ?? '-' }}</p>
                                    <p class="text-gray-600">Status: <x-badge :status="$other->status_pengajuan" /></p>
                                    <p class="text-gray-500">Diajukan: {{ $other->created_at->format('d M Y') }}</p>
                                    <a href="{{ route('marketing.pengajuan-kpr.show', $other->id) }}" class="text-primary hover:text-primary-dark text-xs font-medium">Lihat detail</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Informasi Booking">
                    <div class="space-y-3 text-sm text-gray-700">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Kode Booking</p>
                            <p class="font-semibold text-gray-900">{{ $pengajuan->booking->kode_booking }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Marketing PIC</p>
                            <p class="font-semibold text-gray-900">{{ $pengajuan->booking->marketing?->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Tanggal Booking</p>
                            <p class="text-gray-900">{{ $pengajuan->booking->tanggal_booking?->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card title="Aksi">
                    <div class="space-y-3 text-sm">
                        @if(in_array($pengajuan->status_pengajuan, ['draft', 'ditolak'], true))
                            <a href="{{ route('marketing.pengajuan-kpr.edit', $pengajuan->id) }}"
                                class="block rounded-lg bg-primary px-4 py-3 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                                Edit Pengajuan KPR
                            </a>
                        @endif
                        <a href="{{ route('marketing.booking.show', $pengajuan->booking->id) }}"
                            class="block rounded-lg border border-gray-200 bg-white px-4 py-3 text-center text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                            Kembali ke Booking
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
@endsection
