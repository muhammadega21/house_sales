@props([
    'column' => '',
    'label' => '',
    'sortBy' => '',
    'sortDir' => 'asc',
    'class' => '',
])

@php
    $isActive   = $sortBy === $column;
    $nextDir    = $isActive && $sortDir === 'asc' ? 'desc' : 'asc';
    $currentUrl = request()->fullUrlWithQuery(['sort_by' => $column, 'sort_dir' => $nextDir, 'page' => 1]);
@endphp

@if($column)
    <th {{ $attributes->merge(['class' => 'px-4 py-3 ' . $class]) }}>
        <a href="{{ $currentUrl }}"
           class="group inline-flex items-center gap-1.5 transition-colors hover:text-gray-900">
            {{ $label }}
            <span class="{{ $isActive ? 'text-primary' : 'text-gray-400 opacity-0 group-hover:opacity-100' }} transition">
                @if($isActive && $sortDir === 'asc')
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                    </svg>
                @elseif($isActive && $sortDir === 'desc')
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                @else
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                @endif
            </span>
        </a>
    </th>
@else
    <th {{ $attributes->merge(['class' => 'px-4 py-3 ' . $class]) }}>
        {{ $label }}
    </th>
@endif
