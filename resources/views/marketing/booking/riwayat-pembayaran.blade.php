@props([
    'pembayaran' => collect(),
])

@php
    $totalTerverifikasi = $pembayaran->where('status_verifikasi', 'diverifikasi')->sum('nominal');
@endphp

@if($pembayaran->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($pembayaran as $p)
                    <tr class="hover:bg-gray-50 cursor-pointer"
                        onclick="window.location.href='{{ route('admin.pembayaran.show', $p->id) }}'">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                            {{ $p->tanggal_bayar->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :status="$p->jenis_pembayaran->value" />
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">
                            Rp {{ number_format($p->nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ \App\Enums\MetodeBayar::tryFrom($p->metode_bayar)?->label() ?? $p->metode_bayar ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :status="$p->status_verifikasi->value" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($p->bukti_bayar)
                                <a href="{{ asset('storage/' . $p->bukti_bayar) }}"
                                   target="_blank"
                                   class="text-xs font-medium text-primary hover:text-primary-dark"
                                   onclick="event.stopPropagation()">
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-700">Total Terverifikasi</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-emerald-600">
                        Rp {{ number_format($totalTerverifikasi, 0, ',', '.') }}
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="py-8">
        <x-empty-state title="Belum ada pembayaran"
            message="Belum ada catatan pembayaran untuk booking ini."
            :create-route="''" />
    </div>
@endif