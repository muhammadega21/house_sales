@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">403 - Akses Ditolak</h1>
            <p class="text-gray-600 mb-6">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <a href="{{ url()->previous() }}"
                class="inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition">Kembali</a>
        </div>
    </div>
@endsection
