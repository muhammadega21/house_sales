@props([
    'name',
    'label' => '',
    'accept' => 'image/*',
    'required' => false,
])

@php
    $hasError = $errors->has($name);
    $fileClasses = $hasError
        ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-300 focus:border-primary focus:ring-primary';
@endphp

<div class="mb-4" x-data="{ 
    previewUrl: null,
    fileName: '',
    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            if (file.type.startsWith('image/')) {
                this.previewUrl = URL.createObjectURL(file);
            } else {
                this.previewUrl = null;
            }
        } else {
            this.fileName = '';
            this.previewUrl = null;
        }
    }
}">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="flex flex-col gap-2">
        <div class="relative flex items-center justify-center w-full">
            <label for="{{ $name }}" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100/70 transition-all duration-200 {{ $hasError ? 'border-red-300' : 'border-gray-300' }}">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="mb-1 text-sm text-gray-500"><span class="font-semibold text-primary">Klik untuk upload</span> atau seret file</p>
                    <p class="text-xs text-gray-400" x-text="fileName ? fileName : 'PDF, PNG, JPG (Maks. 5MB/10MB)'"></p>
                </div>
                <input 
                    type="file" 
                    name="{{ $name }}" 
                    id="{{ $name }}" 
                    accept="{{ $accept }}"
                    {{ $required ? 'required' : '' }}
                    @change="handleFileChange($event)"
                    class="hidden"
                >
            </label>
        </div>

        <!-- Alpine Image Preview -->
        <template x-if="previewUrl">
            <div class="relative mt-2 w-32 h-32 rounded-xl overflow-hidden border border-gray-200 shadow-xs">
                <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
                <button type="button" @click="previewUrl = null; fileName = ''; document.getElementById('{{ $name }}').value = ''" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
    @enderror
</div>
