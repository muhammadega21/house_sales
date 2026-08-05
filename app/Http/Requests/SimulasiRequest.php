<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Enums\MetodePembayaran;
use App\Enums\KategoriRumah;
use App\Models\PengaturanSistem;
use App\Models\UnitRumah;

final class SimulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_unit' => ['required', 'integer', 'exists:unit_rumah,id'],
            'metode_pembayaran' => ['required', 'string', 'in:' . implode(',', array_map(fn(MetodePembayaran $method) => $method->value, MetodePembayaran::cases()))],
            'dp_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tenor_tahun' => ['nullable', 'integer', 'min:1', 'max:30'],
            'suku_bunga' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'diskon_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'id_konsumen' => ['nullable', 'integer', 'exists:konsumen,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $unit = UnitRumah::find($this->input('id_unit'));

            if (! $unit) {
                return;
            }

            $dpPersen = $this->input('dp_persen');
            $metode = $this->input('metode_pembayaran');
            $tenor = $this->input('tenor_tahun');
            $sukuBunga = $this->input('suku_bunga');
            $diskon = $this->input('diskon_persen');

            if ($metode === MetodePembayaran::Kpr->value || $metode === MetodePembayaran::CashBertahap->value) {
                if ($dpPersen === null) {
                    $validator->errors()->add('dp_persen', 'DP wajib diisi untuk metode KPR dan Cash Bertahap.');
                }

                if ($tenor === null) {
                    $validator->errors()->add('tenor_tahun', 'Tenor tahun wajib diisi untuk metode ini.');
                }
            }

            if ($metode === MetodePembayaran::Kpr->value) {
                if ($sukuBunga === null) {
                    $validator->errors()->add('suku_bunga', 'Suku bunga wajib diisi untuk metode KPR.');
                }
            }

            if ($metode === MetodePembayaran::CashKeras->value && $diskon !== null && $diskon > 100) {
                $validator->errors()->add('diskon_persen', 'Diskon persentase tidak boleh lebih dari 100%.');
            }

            if (in_array($metode, [MetodePembayaran::Kpr->value, MetodePembayaran::CashBertahap->value], true)) {
                $dpSubsidiMin = (float) PengaturanSistem::getValue('dp_subsidi_min_persen', 1);
                $dpSubsidiMax = (float) PengaturanSistem::getValue('dp_subsidi_max_persen', 5);
                $dpNonSubsidiMin = (float) PengaturanSistem::getValue('dp_non_subsidi_min_persen', 10);
                $dpNonSubsidiMax = (float) PengaturanSistem::getValue('dp_non_subsidi_max_persen', 30);

                if ($unit->kategori === KategoriRumah::Subsidi) {
                    if ($dpPersen === null || $dpPersen < $dpSubsidiMin || $dpPersen > $dpSubsidiMax) {
                        $validator->errors()->add('dp_persen', "DP untuk rumah subsidi harus antara {$dpSubsidiMin}% - {$dpSubsidiMax}%.");
                    }
                } else {
                    if ($dpPersen === null || $dpPersen < $dpNonSubsidiMin || $dpPersen > $dpNonSubsidiMax) {
                        $validator->errors()->add('dp_persen', "DP untuk rumah non-subsidi harus antara {$dpNonSubsidiMin}% - {$dpNonSubsidiMax}%.");
                    }
                }
            }

            if ($metode === MetodePembayaran::Kpr->value && $tenor !== null && $tenor > 20) {
                $validator->errors()->add('tenor_tahun', 'Tenor KPR maksimal 20 tahun.');
            }
        });
    }
}
