@props([
    'columns' => [], // associative array of key => label
    'data' => [], // collection or paginator
    'actions' => [], // array containing 'show', 'edit', 'delete'
    'routePrefix' => '', // e.g. 'admin.users'
])

@php
    $currentSortBy = request('sort_by');
    $currentSortDir = request('sort_dir', 'asc');
    $nextSortDir = $currentSortDir === 'asc' ? 'desc' : 'asc';
    $isPaginator = $data instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $items = $isPaginator ? $data->items() : $data;
@endphp

<div x-data="{
    selectedIds: [],
    allIds: @js(collect($items)->pluck('id')->toArray()),
    toggleAll() {
        if (this.selectedIds.length === this.allIds.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = [...this.allIds];
        }
    }
}" class="w-full">
    <!-- Table Wrapper -->
    <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-xs bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/75">
                <tr>
                    <!-- Checkbox Column -->
                    <th scope="col" class="relative px-6 py-4 text-left w-12">
                        <input type="checkbox" :checked="selectedIds.length === allIds.length && allIds.length > 0"
                            @change="toggleAll()"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer transition-colors duration-200">
                    </th>

                    <!-- Header Columns -->
                    @foreach ($columns as $key => $label)
                        @php
                            $isSortable = !str_contains($key, '.');
                        @endphp
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            @if ($isSortable)
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => $key, 'sort_dir' => $currentSortBy === $key ? $nextSortDir : 'asc', 'page' => 1]) }}"
                                    class="inline-flex items-center gap-1.5 hover:text-gray-900 group transition-colors duration-200">
                                    {{ $label }}
                                    <span
                                        class="text-gray-400 group-hover:text-gray-700 transition-colors duration-200">
                                        @if ($currentSortBy === $key)
                                            @if ($currentSortDir === 'asc')
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                            </svg>
                                        @endif
                                    </span>
                                </a>
                            @else
                                {{ $label }}
                            @endif
                        </th>
                    @endforeach

                    <!-- Actions Column -->
                    @if (!empty($actions))
                        <th scope="col"
                            class="relative px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors duration-150"
                        :class="selectedIds.includes({{ $item->id }}) ? 'bg-blue-50/20' : ''">
                        <!-- Checkbox Cell -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" :value="{{ $item->id }}" x-model="selectedIds"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer transition-colors duration-200">
                        </td>

                        <!-- Data Cells -->
                        @foreach ($columns as $key => $label)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if ($key === 'no')
                                    {{ $isPaginator ? $data->firstItem() + $loop->parent->index : $loop->parent->iteration }}
                                @elseif($key === 'nama_lengkap')
                                    <div class="flex items-center gap-3">
                                        @if ($item->foto_profil)
                                            <img src="{{ asset('storage/' . $item->foto_profil) }}"
                                                alt="{{ $item->nama_lengkap }}"
                                                class="h-12 w-12 rounded-full object-cover ring-2 ring-gray-100">
                                        @else
                                            <div
                                                class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-sm ring-2 ring-gray-100">
                                                {{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->nama_lengkap }}</p>
                                            @if ($item->email)
                                                <p class="text-xs text-gray-500">{{ $item->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($key === 'role')
                                    <x-badge :status="$item->role->value ?? $item->role" />
                                @elseif($key === 'status')
                                    <x-badge :status="$item->status" />
                                @elseif(isset(${$key}))
                                    @if (${$key} instanceof \Closure || (is_object(${$key}) && method_exists(${$key}, '__invoke')))
                                        {{ ${$key}(['item' => $item]) }}
                                    @else
                                        {{ ${$key} }}
                                    @endif
                                @else
                                    {{-- Handle relationship columns like 'perumahan.nama_perumahan' --}}
                                    @if (str_contains($key, '.'))
                                        @php
                                            [$relation, $attribute] = explode('.', $key, 2);
                                        @endphp
                                        {{ $item->{$relation}->{$attribute} ?? '-' }}
                                    @else
                                        {{ $item->{$key} ?? '-' }}
                                    @endif
                                @endif
                            </td>
                        @endforeach

                        <!-- Action Buttons -->
                        @if (!empty($actions))
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex items-center gap-3">
                                    @if (in_array('show', $actions) && $routePrefix)
                                        <a href="{{ route($routePrefix . '.show', $item->id) }}"
                                            class="text-info hover:text-indigo-800 transition-colors duration-200"
                                            title="Detail">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if (in_array('edit', $actions) && $routePrefix)
                                        <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                            class="text-primary hover:text-primary-dark transition-colors duration-200"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if (in_array('delete', $actions) && $routePrefix)
                                        <x-confirm-delete :route="route($routePrefix . '.destroy', $item->id)" :item-name="$item->nama ??
                                            ($item->nama_lengkap ?? ($item->kode_unit ?? 'item ini'))" />
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <!-- Empty State -->
                    <tr>
                        <td colspan="{{ count($columns) + (empty($actions) ? 1 : 2) }}" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="p-3 bg-gray-100 text-gray-400 rounded-full">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-3.586-3.586a2 2 0 00-2.828 0L16 9.586l-3.586-3.586a2 2 0 00-2.828 0L6 9.586m0 0l-3-3" />
                                    </svg>
                                </div>
                                <div class="max-w-xs">
                                    <h4 class="text-sm font-semibold text-gray-900">Tidak ada data</h4>
                                    <p class="text-xs text-gray-500 mt-1">Silakan tambahkan data baru atau sesuaikan
                                        filter pencarian Anda.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if ($isPaginator)
        <div class="mt-4">
            <x-pagination :paginator="$data" />
        </div>
    @endif
</div>
