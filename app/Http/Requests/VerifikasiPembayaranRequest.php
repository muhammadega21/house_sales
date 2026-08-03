<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusVerifikasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class VerifikasiPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_verifikasi' => ['required', Rule::in([
                StatusVerifikasi::Diverifikasi->value,
                StatusVerifikasi::Ditolak->value,
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

            if ($status === StatusVerifikasi::Ditolak->value && empty($catatan)) {
                $validator->errors()->add(
                    'catatan_verifikasi',
                    'Catatan/alasan penolakan wajib diisi saat status Ditolak.'
                );
            }
        });
    }
}
