@props([
    'type' => null,
    'message' => null,
])

@php
    // Auto-read from session if no explicit props are passed
    $alerts = [];

    if ($type && $message) {
        $alerts[] = ['type' => $type, 'message' => $message];
    }

    foreach (['success', 'error', 'warning', 'info'] as $sessionType) {
        if (session()->has($sessionType)) {
            $alerts[] = ['type' => $sessionType, 'message' => session($sessionType)];
        }
    }
@endphp

@foreach($alerts as $alert)
    @php
        $alertType = $alert['type'];
        $alertMessage = $alert['message'];

        $typeClasses = match($alertType) {
            'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'error', 'danger' => 'bg-red-50 text-red-800 border-red-200',
            'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
            'info' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            default => 'bg-gray-50 text-gray-800 border-gray-200',
        };

        $iconClasses = match($alertType) {
            'success' => 'text-emerald-500',
            'error', 'danger' => 'text-red-500',
            'warning' => 'text-amber-500',
            'info' => 'text-indigo-500',
            default => 'text-gray-500',
        };
    @endphp

    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         class="flex items-center p-4 mb-4 border rounded-xl {{ $typeClasses }} shadow-sm transition-all duration-300"
         role="alert"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
    >
        <!-- Icon -->
        <div class="mr-3 shrink-0">
            @if($alertType === 'success')
                <svg class="w-5 h-5 {{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($alertType === 'error' || $alertType === 'danger')
                <svg class="w-5 h-5 {{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($alertType === 'warning')
                <svg class="w-5 h-5 {{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            @else
                <svg class="w-5 h-5 {{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>

        <!-- Message -->
        <div class="text-sm font-medium flex-1">
            {{ $alertMessage }}
        </div>

        <!-- Dismiss Button -->
        <button @click="show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 hover:bg-black/5 inline-flex h-8 w-8 items-center justify-center transition-colors duration-200">
            <span class="sr-only">Tutup</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endforeach
