@props([
    'searchPlaceholder' => 'Cari data...',
    'searchRoute' => '',
    'perPage' => 10,
    'total' => 0,
    'filtered' => 0,
    'search' => '',
    'hasFilters' => false,
    'excludeKeys' => [],
])

@php
    $hiddenExclude = array_merge(['search', 'page', 'per_page'], $excludeKeys);
@endphp

<div x-data="{
    search: @js($search),
    loading: false,
    debounceTimer: null,
    submitSearch() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.loading = true;
            this.$refs.searchInput.closest('form').submit();
        }, 300);
    }
}" class="space-y-4">
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-xs">
        <form method="GET" action="{{ $searchRoute }}" x-ref="toolbarForm" @submit="loading = true" class="space-y-4">
            @foreach (request()->except($hiddenExclude) as $key => $value)
                @if (!is_array($value) && !is_null($value) && $value !== '')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:flex-wrap">
                {{-- Search Input --}}
                <div class="flex-1 min-w-[220px]">
                    <label for="search" class="mb-1 block text-sm font-medium text-gray-700">Pencarian</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" x-ref="searchInput" x-model="search"
                            @input="submitSearch()" placeholder="{{ $searchPlaceholder }}"
                            class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm transition focus:border-primary focus:ring-primary">
                        <button type="button" x-show="search.length > 0" x-cloak
                            @click="search = ''; loading = true; $refs.searchInput.closest('form').submit()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Filter Slots --}}
                {{ $filters ?? '' }}

                {{-- Per Page Selector --}}
                <div class="min-w-[120px]">
                    <label for="per_page" class="mb-1 block text-sm font-medium text-gray-700">Per halaman</label>
                    <select name="per_page" id="per_page" onchange="this.closest('form').submit()"
                        class="block w-full rounded-lg border border-gray-300 py-2.5 text-sm transition focus:border-primary focus:ring-primary">
                        @foreach ([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" {{ (int) $perPage === $option ? 'selected' : '' }}>
                                {{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2">
                    <button type="submit" :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark disabled:opacity-60">
                        <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg x-show="!loading" class="mr-1 -ml-0.5 inline h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Terapkan
                    </button>
                    @if ($hasFilters)
                        <a href="{{ $searchRoute }}"
                            class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            <svg class="mr-1 -ml-0.5 inline h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if ($search)
        <div class="flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2.5 text-sm text-blue-700">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Hasil pencarian untuk: <strong>"{{ $search }}"</strong></span>
        </div>
    @endif

    @if ($total > 0)
        <div class="text-sm text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $filtered }}</span> dari <span
                class="font-semibold text-gray-700">{{ $total }}</span> data
        </div>
    @endif
</div>
