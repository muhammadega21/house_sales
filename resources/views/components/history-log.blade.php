@props(['histories', 'showUser' => true, 'showDate' => true])

<div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Riwayat Perubahan Status</h2>
            <p class="mt-1 text-sm text-gray-500">Log perubahan status terakhir untuk booking ini.</p>
        </div>
    </div>

    @if ($histories->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
            Belum ada riwayat status.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        @if ($showDate)
                            <th class="px-4 py-3">Tanggal</th>
                        @endif
                        <th class="px-4 py-3">Status Sebelum</th>
                        <th class="px-4 py-3">Status Sesudah</th>
                        <th class="px-4 py-3">Catatan</th>
                        @if ($showUser)
                            <th class="px-4 py-3">Diubah Oleh</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($histories as $index => $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $index + 1 }}</td>
                            @if ($showDate)
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $history->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            @endif
                            <td class="px-4 py-3 text-gray-700">{{ $history->getStatusSebelumLabel() }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->getStatusSesudahLabel() }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->catatan ?? '-' }}</td>
                            @if ($showUser)
                                <td class="px-4 py-3 text-gray-700">{{ $history->user?->nama_lengkap ?? '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
