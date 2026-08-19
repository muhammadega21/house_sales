@props(['konsumen'])

@if ($konsumen)
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xs">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="text-sm font-semibold text-gray-900">{{ $konsumen->nama_lengkap }}</h4>
                <p class="text-xs text-gray-500">NIK: {{ $konsumen->nik }}</p>
                <p class="text-xs text-gray-500">{{ $konsumen->no_hp }}</p>
                @if ($konsumen->email)
                    <p class="text-xs text-gray-500">{{ $konsumen->email }}</p>
                @endif
            </div>
        </div>
        <div class="mt-3 border-t border-gray-100 pt-3">
            <p class="text-xs text-gray-500">{{ $konsumen->alamat_lengkap }}</p>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.konsumen.show', $konsumen->id) }}"
                class="text-xs font-medium text-primary hover:text-primary-dark">
                Lihat Detail Konsumen →
            </a>
        </div>
    </div>
@else
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xs">
        <p class="text-sm text-gray-400">Data konsumen tidak tersedia</p>
    </div>
@endif
