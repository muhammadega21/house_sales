<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PengajuanKprRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_booking' => ['required', 'integer', 'exists:booking,id'],
            'nama_bank' => ['required', 'string', 'max:100'],
            'plafon_kpr' => ['required', 'numeric', 'min:0'],
            'tenor_tahun' => ['required', 'integer', 'min:1', 'max:30'],
            'suku_bunga' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tanggal_pengajuan' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
