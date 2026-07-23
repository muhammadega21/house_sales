@props([
    'id',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl, 2xl
])

@php
    $maxWidth = match($size) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        default => 'sm:max-w-md',
    };
@endphp

<div x-data="{ open: false }"
     x-show="open"
     x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
     x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
     x-on:keydown.escape.window="open = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Backdrop with click-to-close -->
    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidth }}"
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Modal Header -->
            <div class="bg-white px-4 pt-5 pb-4 border-b border-gray-100 sm:px-6 sm:py-4 flex justify-between items-center">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    {{ $title }}
                </h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                    <span class="sr-only">Tutup</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="bg-white px-4 py-5 sm:p-6">
                {{ $slot }}
            </div>

            <!-- Modal Footer (Optional slot) -->
            @if(isset($footer))
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2 border-t border-gray-100">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
