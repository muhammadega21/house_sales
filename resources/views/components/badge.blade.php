@props([
    'status' => '',
    'color' => null,
])

@php
    $rawStatus = $status;
    if ($status instanceof \BackedEnum) {
        $status = $status->value;
    }
    $normalizedStatus = strtolower(trim((string)$status));

    // Default mapping based on the UI/UX Guidelines
    $badgeColor = $color ?? match($normalizedStatus) {
        'tersedia', 'serah_terima', 'diverifikasi', 'valid', 'aktif', 'manajemen' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'dibooking', 'booking', 'pending', 'baru', 'dihubungi', 'berminat' => 'bg-amber-100 text-amber-800 border-amber-200',
        'dijual', 'akad', 'marketing' => 'bg-blue-100 text-blue-800 border-blue-200',
        'dibatalkan', 'batal', 'ditolak', 'tidak_valid', 'perlu_revisi', 'non_aktif', 'admin' => 'bg-red-100 text-red-800 border-red-200',
        'pengajuan_kpr', 'diajukan', 'verifikasi_bank' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'prospek', 'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
        default => 'bg-gray-100 text-gray-800 border-gray-200',
    };

    // Label formatting: replace underscore with space and capitalize
    $label = match($normalizedStatus) {
        'pengajuan_kpr' => 'Pengajuan KPR',
        'ready_stock' => 'Ready Stock',
        default => ucwords(str_replace('_', ' ', $normalizedStatus)),
    };
@endphp

<span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold border {{ $badgeColor }} transition-colors duration-150">
    {{ $label }}
</span>
