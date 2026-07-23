<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PerumahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_perumahan' => ['required', 'string', 'max:150'],
            'alamat' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:50'],
            'provinsi' => ['required', 'string', 'max:50'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'deskripsi' => ['nullable', 'string'],
            'foto_kawasan' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', Rule::in(['aktif', 'non_aktif'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_perumahan.required' => 'Nama perumahan wajib diisi.',
            'nama_perumahan.max' => 'Nama perumahan maksimal 150 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'kota.required' => 'Kota wajib diisi.',
            'kota.max' => 'Kota maksimal 50 karakter.',
            'provinsi.required' => 'Provinsi wajib diisi.',
            'provinsi.max' => 'Provinsi maksimal 50 karakter.',
            'kode_pos.max' => 'Kode pos maksimal 10 karakter.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'foto_kawasan.image' => 'Foto kawasan harus berupa gambar.',
            'foto_kawasan.max' => 'Ukuran foto kawasan maksimal 5MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}