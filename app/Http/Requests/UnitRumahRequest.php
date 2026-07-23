<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UnitRumahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unitRumah = $this->route('unit_rumah');

        return [
            'id_perumahan' => ['required', 'exists:perumahan,id'],
            'kode_unit' => [
                'required', 'string', 'max:20',
                Rule::unique('unit_rumah', 'kode_unit')
                    ->where('id_perumahan', $this->input('id_perumahan'))
                    ->ignore($unitRumah),
            ],
            'tipe_rumah' => ['required', 'string', 'max:50'],
            'kategori' => ['required', Rule::in(['subsidi', 'non_subsidi'])],
            'jenis_ketersediaan' => ['required', Rule::in(['ready_stock', 'indent'])],
            'luas_tanah' => ['required', 'numeric', 'min:0'],
            'luas_bangunan' => ['required', 'numeric', 'min:0', 'lte:luas_tanah'],
            'jumlah_kamar_tidur' => ['nullable', 'integer', 'min:0'],
            'jumlah_kamar_mandi' => ['nullable', 'integer', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:1'],
            'dp_minimum_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'foto_unit' => ['nullable', 'image', 'max:5120'],
            'denah_unit' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tanggal_selesai_bangun' => [
                'nullable', 'date', 'after:today',
                Rule::requiredIf($this->input('jenis_ketersediaan') === 'indent'),
            ],
        ];
    }
}
