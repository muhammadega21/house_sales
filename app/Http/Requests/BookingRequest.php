<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\KategoriRumah;
use App\Enums\MetodeBayar;
use App\Enums\StatusPembayaranFee;
use App\Enums\StatusUnit;
use App\Models\UnitRumah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $bookingId = $isUpdate ? $this->route('booking') : null;

        $unit = UnitRumah::find($this->input('id_unit'));
        $minFee = $unit ? ($unit->kategori === KategoriRumah::Subsidi ? 1000000 : 5000000) : 0;

        return [
            'id_konsumen' => ['required', 'exists:konsumen,id'],
            'id_unit' => ['required', 'exists:unit_rumah,id'],
            'tanggal_booking' => ['required', 'date'],
            'booking_fee' => ['required', 'numeric', "min:{$minFee}"],
            'metode_bayar_fee' => ['nullable', Rule::in(array_map(fn($m) => $m->value, MetodeBayar::cases()))],
            'bukti_bayar_fee' => ['nullable', 'image', 'max:5120'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_fee.min' => 'Booking fee minimum untuk rumah subsidi adalah Rp 1.000.000',
        ];
    }

    public function withValidator($validator): void
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $validator->after(function ($validator) use ($isUpdate) {
            $unit = UnitRumah::find($this->input('id_unit'));

            if ($unit && !$isUpdate && $unit->status_unit !== StatusUnit::Tersedia) {
                $validator->errors()->add('id_unit', 'Unit harus berstatus tersedia untuk dapat dibooking.');
            }

            if ($unit && $this->input('booking_fee')) {
                $minFee = $unit->kategori === KategoriRumah::Subsidi
                    ? 1000000
                    : 5000000;

                if ($this->input('booking_fee') < $minFee) {
                    $validator->errors()->add('booking_fee', "Booking fee minimum untuk kategori {$unit->kategori->label()} adalah Rp " . number_format($minFee, 0, ',', '.'));
                }
            }

            if (Auth::check() && Auth::user()->role?->value === 'marketing') {
                $konsumen = \App\Models\Konsumen::find($this->input('id_konsumen'));
                if ($konsumen && $konsumen->id_marketing !== Auth::id()) {
                    $validator->errors()->add('id_konsumen', 'Anda hanya bisa booking untuk konsumen milik Anda sendiri.');
                }
            }
        });
    }
}