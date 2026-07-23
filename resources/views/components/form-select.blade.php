@props([
    'name',
    'label' => '',
    'options' => [], // array of key => label
    'selected' => null,
    'required' => false,
])

@php
    $hasError = $errors->has($name);
    $selectClasses = $hasError
        ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-300 focus:border-primary focus:ring-primary';
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => "block w-full rounded-lg text-sm shadow-xs transition-colors duration-200 focus:ring-2 focus:ring-offset-0 focus:outline-none p-2.5 border bg-white {$selectClasses}"
        ]) }}
    >
        <option value="">Pilih {{ $label ?: 'Pilihan' }}</option>
        @foreach($options as $key => $optionLabel)
            @php
                $currentVal = old($name, $selected);
                $isSel = (string)$key === (string)$currentVal;
            @endphp
            <option value="{{ $key }}" {{ $isSel ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
    @enderror
</div>
