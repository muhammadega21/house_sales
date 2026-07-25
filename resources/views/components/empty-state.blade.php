@props([
    'title' => 'Belum ada data',
    'message' => 'Silakan tambahkan data baru atau sesuaikan filter pencarian Anda.',
    'search' => '',
    'createRoute' => '',
    'createLabel' => 'Tambah Data',
    'resetRoute' => '',
    'icon' => 'table', // table, search
])

<div class="flex flex-col items-center justify-center gap-4 px-6 py-16 text-center">
    {{-- Icon --}}
    @if($icon === 'search' || $search)
        <div class="rounded-full bg-amber-50 p-4 text-amber-400">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    @else
        <div class="rounded-full bg-gray-100 p-4 text-gray-400">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-3.586-3.586a2 2 0 00-2.828 0L16 9.586l-3.586-3.586a2 2 0 00-2.828 0L6 9.586m0 0l-3-3" />
            </svg>
        </div>
    @endif

    {{-- Text --}}
    <div class="max-w-sm">
        @if($search)
            <h4 class="text-base font-semibold text-gray-900">Tidak ditemukan hasil</h4>
            <p class="mt-1 text-sm text-gray-500">Tidak ada data yang cocok untuk pencarian <strong class="text-gray-700">"{{ $search }}"</strong>. Coba kata kunci lain.</p>
        @else
            <h4 class="text-base font-semibold text-gray-900">{{ $title }}</h4>
            <p class="mt-1 text-sm text-gray-500">{{ $message }}</p>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="mt-2 flex flex-wrap items-center justify-center gap-3">
        @if($search || $resetRoute)
            <a href="{{ $resetRoute ?: url()->current() }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset Filter
            </a>
        @endif

        @if($createRoute)
            <a href="{{ $createRoute }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ $createLabel }}
            </a>
        @endif
    </div>
</div>
