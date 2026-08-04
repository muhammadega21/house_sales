<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusVerifikasiDokumen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class VerifikasiDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role?->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'status_verifikasi' => ['required', Rule::in([
                StatusVerifikasiDokumen::Valid->value,
                StatusVerifikasiDokumen::TidakValid->value,
                StatusVerifikasiDokumen::PerluRevisi->value,
            ])],
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.in' => 'Status verifikasi tidak valid.',
            'catatan_verifikasi.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('catatan_verifikasi') && is_string($this->input('catatan_verifikasi'))) {
            $value = trim($this->input('catatan_verifikasi'));
            $this->merge(['catatan_verifikasi' => $value === '' ? null : $value]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status = $this->input('status_verifikasi');
            $catatan = $this->input('catatan_verifikasi');

            if (in_array($status, [
                StatusVerifikasiDokumen::TidakValid->value,
                StatusVerifikasiDokumen::PerluRevisi->value,
            ], true) && empty($catatan)) {
                $validator->errors()->add(
                    'catatan_verifikasi',
                    'Catatan verifikasi wajib diisi ketika status Tidak Valid atau Perlu Revisi.'
                );
            }
        });
    }
}
