<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class KonsumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $konsumenId = $isUpdate ? $this->route('konsumen') : null;

        return [
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik' => [
                'required',
                'string',
                'digits_between:16,16',
                Rule::unique('konsumen', 'nik')->ignore($konsumenId),
            ],
            'no_kk' => ['nullable', 'string', 'digits_between:16,16'],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'alamat_lengkap' => ['required', 'string'],
            'tempat_lahir' => ['nullable', 'string', 'max:50'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'status_pernikahan' => ['nullable', Rule::in(['belum_menikah', 'menikah', 'cerai_hidup', 'cerai_mati'])],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:100'],
            'penghasilan_bulanan' => ['nullable', 'numeric', 'min:0'],
            'npwp' => ['nullable', 'string', 'max:15'],
            'foto_ktp' => ['nullable', 'image', 'max:5120'],
            'foto_kk' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (Auth::check() && Auth::user()->role?->value === 'marketing') {
                $this->merge(['id_marketing' => Auth::id()]);
            }
        });
    }
}
