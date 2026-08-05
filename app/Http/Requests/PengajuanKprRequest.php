<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusPenjualan;
use App\Models\Booking;
use App\Services\DokumenService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

final class PengajuanKprRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.pengajuan-kpr.proses-update-status')) {
            return [
                'status_pengajuan' => ['required', 'string', 'in:draft,diajukan,verifikasi_bank,disetujui,ditolak,akad,batal'],
                'catatan' => ['required', 'string', 'max:1000'],
            ];
        }

        $rules = [
            'nama_bank' => ['required', 'string', 'max:100'],
            'plafon_kpr' => ['required', 'numeric', 'min:0'],
            'tenor_tahun' => ['required', 'integer', 'min:1', 'max:30'],
            'suku_bunga' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tanggal_pengajuan' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'status_pengajuan' => ['nullable', 'string', 'in:draft,diajukan,verifikasi_bank,disetujui,ditolak,akad,batal'],
        ];

        if ($this->isMethod('post')) {
            $rules['id_booking'] = ['required', 'integer', 'exists:booking,id'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        if (! $this->isMethod('post')) {
            return;
        }

        $validator->after(function (Validator $validator): void {
            if (! $this->filled('id_booking')) {
                return;
            }

            $booking = Booking::with('statusPenjualan')->find($this->input('id_booking'));
            if (! $booking) {
                return;
            }

            if ($booking->id_marketing !== auth()->id()) {
                $validator->errors()->add('id_booking', 'Booking tidak ditemukan atau tidak dimiliki oleh Anda.');
                return;
            }

            $statusSaatIni = $booking->statusPenjualan?->status_saat_ini;
            $statusValue = $statusSaatIni instanceof StatusPenjualan ? $statusSaatIni->value : (string) ($statusSaatIni ?? '');
            if ($statusValue !== StatusPenjualan::Booking->value) {
                $validator->errors()->add('id_booking', 'Booking harus berstatus booking sebelum membuat pengajuan KPR.');
                return;
            }

            if ($booking->pengajuanKpr()->exists()) {
                $validator->errors()->add('id_booking', 'Booking ini sudah memiliki pengajuan KPR.');
                return;
            }

            if (! app(DokumenService::class)->isComplete($booking->id_konsumen)) {
                $validator->errors()->add('id_booking', 'Dokumen KPR konsumen belum lengkap atau belum valid.');
            }
        });
    }
}
