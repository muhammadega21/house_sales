<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\JenisDokumen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

final class DokumenKprRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jenis = $this->input('jenis_dokumen');
        $maxSize = $this->getMaxSize($jenis);

        return [
            'id_konsumen' => ['required', 'exists:konsumen,id'],
            'jenis_dokumen' => ['required', 'in:' . implode(',', array_map(fn(JenisDokumen $item): string => $item->value, JenisDokumen::cases()))],
            'file_dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . $maxSize],
        ];
    }

    public function messages(): array
    {
        return [
            'id_konsumen.required' => 'Konsumen wajib dipilih.',
            'id_konsumen.exists' => 'Konsumen tidak ditemukan.',
            'jenis_dokumen.required' => 'Jenis dokumen wajib dipilih.',
            'jenis_dokumen.in' => 'Jenis dokumen tidak valid.',
            'file_dokumen.required' => 'File dokumen wajib diupload.',
            'file_dokumen.file' => 'File dokumen tidak valid.',
            'file_dokumen.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'file_dokumen.max' => 'Ukuran file melebihi batas yang diizinkan.',
        ];
    }

    public function after(): array
    {
        $idKonsumen = $this->input('id_konsumen');
        $jenis = $this->input('jenis_dokumen');

        return [
            function ($validator) use ($idKonsumen, $jenis): void {
                if (!$idKonsumen || !$jenis) {
                    return;
                }

                $exists = DB::table('dokumen_kpr')
                    ->where('id_konsumen', $idKonsumen)
                    ->where('jenis_dokumen', $jenis)
                    ->exists();

                if ($exists && $jenis !== 'lainnya') {
                    $validator->errors()->add('jenis_dokumen', 'Dokumen dengan jenis ini sudah pernah diupload untuk konsumen ini.');
                }
            },
        ];
    }

    private function getMaxSize(?string $jenis): int
    {
        $jenisEnum = $jenis ? JenisDokumen::tryFrom($jenis) : null;
        if (!$jenisEnum) {
            return 10240;
        }

        return $jenisEnum->maxsize();
    }
}
