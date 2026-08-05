@props([
    'status' => null,
])

@php
    $steps = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'verifikasi_bank' => 'Verifikasi Bank',
        'disetujui' => 'Disetujui',
        'akad' => 'Akad',
        'batal' => 'Batal',
    ];
    $current = $status ? array_search($status, array_keys($steps), true) : null;
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between text-sm text-gray-500">
        @foreach($steps as $key => $label)
            <div class="flex-1 text-center">
                <span class="font-semibold {{ $status === $key ? 'text-gray-900' : 'text-gray-500' }}">
                    {{ $label }}
                </span>
            </div>
        @endforeach
    </div>
    <div class="relative h-2 rounded-full bg-gray-200 overflow-hidden">
        @php
            $percent = 0;
            if ($current !== false && $current !== null) {
                $percent = round((($current + 1) / count($steps)) * 100);
            }
        @endphp
        <div class="h-full rounded-full bg-primary transition-all duration-500" style="width: {{ $percent }}%"></div>
    </div>
</div>
