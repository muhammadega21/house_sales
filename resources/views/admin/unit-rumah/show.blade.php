@extends('layouts.app')

@section('content')
    <div class="space-y-6" x-data="{ lightbox: false, lightSrc: '' }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Unit {{ $unitRumah->kode_unit }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $unitRumah->perumahan->nama_perumahan }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.unit-rumah.edit', $unitRumah) }}"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit</a>
                <a href="{{ route('admin.unit-rumah.index') }}"
                    class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">Kembali</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-2">
                <div class="space-y-5">
                    @if ($unitRumah->foto_unit)
                        <img src="{{ asset('storage/' . $unitRumah->foto_unit) }}" alt="{{ $unitRumah->kode_unit }}"
                            class="h-80 w-full rounded-xl object-cover cursor-zoom-in"
                            @click="lightSrc='{{ asset('storage/' . $unitRumah->foto_unit) }}'; lightbox = true">
                    @else
                        <div class="flex h-80 items-center justify-center rounded-xl bg-gray-100 text-gray-400">Foto unit
                            belum tersedia</div>
                    @endif

                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Detail Unit</h2>
                            <x-badge :status="$unitRumah->status_unit->value" />
                        </div>

                        <dl class="grid gap-4 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-gray-500">Perumahan</dt>
                                <dd class="mt-1 font-medium">{{ $unitRumah->perumahan->nama_perumahan }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kode / Tipe</dt>
                                <dd class="mt-1 font-medium">{{ $unitRumah->kode_unit }} / {{ $unitRumah->tipe_rumah }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kategori</dt>
                                <dd class="mt-1"><x-badge :status="$unitRumah->kategori->value" /></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Ketersediaan</dt>
                                <dd class="mt-1 font-medium">{{ $unitRumah->jenis_ketersediaan->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Luas Tanah / Bangunan</dt>
                                <dd class="mt-1 font-medium">{{ $unitRumah->luas_tanah }} m² /
                                    {{ $unitRumah->luas_bangunan }} m²</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kamar Tidur / Mandi</dt>
                                <dd class="mt-1 font-medium">{{ $unitRumah->jumlah_kamar_tidur ?? '-' }} /
                                    {{ $unitRumah->jumlah_kamar_mandi ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Harga Jual</dt>
                                <dd class="mt-1 font-medium">Rp
                                    {{ number_format((float) $unitRumah->harga_jual, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">DP Minimum</dt>
                                <dd class="mt-1 font-medium">
                                    {{ $unitRumah->dp_minimum_persen !== null ? $unitRumah->dp_minimum_persen . '%' : '-' }}
                                </dd>
                            </div>

                            @if ($unitRumah->jenis_ketersediaan === \App\Enums\JenisKetersediaan::Indent)
                                <div>
                                    <dt class="text-gray-500">Selesai Bangun</dt>
                                    <dd class="mt-1 font-medium">
                                        {{ $unitRumah->tanggal_selesai_bangun?->format('d M Y') ?? '-' }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </x-card>

            <div class="space-y-6">
                <x-card title="Status Unit">
                    <div class="py-4 text-center">
                        <span
                            class="inline-flex rounded-full px-4 py-2 text-base font-bold {{ $unitRumah->status_unit->color() === 'success' ? 'bg-green-100 text-green-800' : ($unitRumah->status_unit->color() === 'warning' ? 'bg-amber-100 text-amber-800' : ($unitRumah->status_unit->color() === 'danger' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">{{ $unitRumah->status_unit->label() }}</span>
                    </div>
                </x-card>

                <x-card title="Denah Unit">
                    @if ($unitRumah->denah_unit)
                        @if (str_ends_with(strtolower($unitRumah->denah_unit), '.pdf'))
                            <a href="{{ asset('storage/' . $unitRumah->denah_unit) }}" target="_blank" rel="noopener"
                                class="font-semibold text-primary hover:underline">Lihat denah PDF</a>
                        @else
                            <img src="{{ asset('storage/' . $unitRumah->denah_unit) }}"
                                alt="Denah {{ $unitRumah->kode_unit }}"
                                class="w-full rounded-xl object-cover cursor-zoom-in"
                                @click="lightSrc='{{ asset('storage/' . $unitRumah->denah_unit) }}'; lightbox = true">
                        @endif
                    @else
                        <p class="text-sm text-gray-500">Denah belum tersedia.</p>
                    @endif
                </x-card>
            </div>

        </div>

        <!-- Riwayat Booking -->
        <x-card title="Riwayat Booking">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Konsumen</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $booking)
                            <tr>
                                <td class="px-4 py-4">{{ $booking->konsumen->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $booking->tanggal_booking?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $booking->status_pembayaran_fee }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-gray-500">Belum ada riwayat booking.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-slot:footer>
                <x-pagination :paginator="$bookings" />
            </x-slot:footer>
        </x-card>

        <!-- Lightbox Modal -->
        <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
            @click.self="lightbox = false">
            <div class="max-w-4xl max-h-[90vh] overflow-auto p-4">
                <img :src="lightSrc" alt="Preview"
                    class="max-w-full max-h-[85vh] rounded-lg object-contain shadow-lg">
            </div>
        </div>

    </div>
@endsection
