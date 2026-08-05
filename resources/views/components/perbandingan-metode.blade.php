@props([
    'hasilKpr' => null,
    'hasilCashBertahap' => null,
    'hasilCashKeras' => null,
    'highlightBest' => true,
])

@php
    $methods = [
        ['key' => 'kpr', 'label' => 'KPR', 'data' => $hasilKpr],
        ['key' => 'cash_bertahap', 'label' => 'Cash Bertahap', 'data' => $hasilCashBertahap],
        ['key' => 'cash_keras', 'label' => 'Cash Keras', 'data' => $hasilCashKeras],
    ];

    $bestMethod = null;
    if ($highlightBest) {
        $totals = [];
        foreach ($methods as $method) {
            if (is_array($method['data']) && isset($method['data']['total_pembayaran'])) {
                $totals[$method['key']] = $method['data']['total_pembayaran'];
            }
        }

        if (!empty($totals)) {
            asort($totals);
            $bestMethod = array_key_first($totals);
        }
    }

    $formatRp = fn($value) => 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
@endphp

<div class="space-y-4">
    <div class="grid gap-4 xl:grid-cols-3">
        @foreach ($methods as $method)
            @php $data = $method['data']; @endphp
            <div
                class="rounded-2xl border p-5 {{ $bestMethod === $method['key'] ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 bg-white' }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $method['label'] }}</p>
                        @if ($bestMethod === $method['key'])
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-1 text-[11px] font-semibold text-emerald-700">Rekomendasi
                                Terbaik</span>
                        @endif
                    </div>
                    <span
                        class="text-xs uppercase tracking-wider text-slate-500">{{ optional($data)['metode'] ?? '' }}</span>
                </div>

                <div class="mt-5 space-y-3 text-sm text-slate-700">
                    <p class="text-lg font-semibold text-slate-900">
                        {{ $data ? $formatRp($data['total_pembayaran']) : '-' }}</p>
                    <p>Total Bunga: {{ $data ? $formatRp($data['total_bunga'] ?? 0) : '-' }}</p>
                    <p>Cicilan Bulanan: {{ $data ? $formatRp($data['cicilan_bulanan'] ?? 0) : '-' }}</p>
                    <p>DP:
                        {{ $data ? sprintf('%s%% (%s)', $data['dp_persen'] ?? 0, $formatRp($data['dp_nominal'] ?? 0)) : '-' }}
                    </p>
                    @if ($data && isset($data['tenor_tahun']) && $data['tenor_tahun'] > 0)
                        <p>Tenor: {{ $data['tenor_tahun'] }} tahun</p>
                    @endif
                    @if ($data && isset($data['diskon_persen']))
                        <p>Diskon: {{ $data['diskon_persen'] }}%</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
