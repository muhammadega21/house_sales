<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusProspek;
use App\Enums\SumberProspek;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProspekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_prospek' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'sumber_prospek' => ['nullable', Rule::in(array_map(fn($s) => $s->value, SumberProspek::cases()))],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'status_prospek' => ['required', Rule::in(array_map(fn($s) => $s->value, StatusProspek::cases()))],
            'tanggal_prospek' => ['required', 'date'],
            'id_marketing' => ['nullable', 'exists:users,id'],
        ];
    }
}
