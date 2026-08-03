<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\JenisPembayaran;
use App\Enums\MetodeBayar;
use App\Enums\StatusVerifikasi;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_booking' => ['required', 'exists:booking,id'],
            'jenis_pembayaran' => ['required', Rule::in(array_map(fn($s) => $s->value, JenisPembayaran::cases()))],
            'nominal' => ['required', 'numeric', 'min:1'],
            'tanggal_bayar' => ['required', 'date', 'before_or_equal:today'],
            'metode_bayar' => ['required', Rule::in(array_map(fn($m) => $m->value, MetodeBayar::cases()))],
            'no_referensi' => ['nullable', 'string', 'max:50'],
            'bukti_bayar' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_booking.required' => 'Booking wajib dipilih.',
            'id_booking.exists' => 'Booking yang dipilih tidak valid.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih.',
            'nominal.required' => 'Nominal pembayaran wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal pembayaran harus lebih dari 0.',
            'tanggal_bayar.required' => 'Tanggal pembayaran wajib diisi.',
            'tanggal_bayar.max' => 'Tanggal pembayaran tidak boleh melebihi hari ini.',
            'metode_bayar.required' => 'Metode pembayaran wajib dipilih.',
            'no_referensi.max' => 'No. referensi maksimal 50 karakter.',
            'bukti_bayar.required' => 'Bukti pembayaran wajib diupload (BR-18).',
            'bukti_bayar.mimes' => 'Bukti pembayaran harus berupa gambar (JPG, PNG) atau PDF.',
            'bukti_bayar.max' => 'Ukuran bukti pembayaran tidak boleh melebihi 5MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $metodeBayar = $this->input('metode_bayar');
            $noReferensi = $this->input('no_referensi');

            if ($metodeBayar === MetodeBayar::Transfer->value && empty($noReferensi)) {
                $validator->errors()->add('no_referensi', 'No. referensi wajib diisi saat metode bayar adalah Transfer.');
            }

            $jenisPembayaran = $this->input('jenis_pembayaran');
            $idBooking = $this->input('id_booking');
            $nominal = $this->input('nominal');

            $booking = $idBooking ? Booking::find($idBooking) : null;

            if ($booking && $jenisPembayaran === JenisPembayaran::Dp->value && $nominal !== null) {
                $unit = $booking->unit;
                if ($unit && $unit->dp_minimum_persen) {
                    $dpMinimum = (float) $unit->harga_jual * ((float) $unit->dp_minimum_persen / 100);
                    if ((float) $nominal <= $dpMinimum) {
                        $validator->errors()->add(
                            'nominal',
                            'Nominal DP minimal adalah Rp ' . number_format($dpMinimum, 0, ',', '.') . ' (' . $unit->dp_minimum_persen . '% dari harga unit).'
                        );
                    }
                }
            }

            if ($booking && $jenisPembayaran === JenisPembayaran::Cicilan->value) {
                $hasVerifiedDp = $booking->pembayaran()
                    ->where('jenis_pembayaran', JenisPembayaran::Dp->value)
                    ->where('status_verifikasi', StatusVerifikasi::Diverifikasi->value)
                    ->exists();

                if (! $hasVerifiedDp) {
                    $validator->errors()->add(
                        'jenis_pembayaran',
                        'Pembayaran cicilan hanya dapat dilakukan setelah DP yang sudah diverifikasi.'
                    );
                }
            }
        });
    }
}
