@props([
    'title' => '',
    'subtitle' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden']) }}>
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            @if($title)
                <h3 class="text-base font-semibold text-gray-900 leading-6">
                    {{ $title }}
                </h3>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-gray-500 leading-5">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif

    <div class="px-6 py-5">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endif
</div>
