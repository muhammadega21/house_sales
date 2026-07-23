<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MarketingTargetRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $targetId = \App\Models\MarketingTarget::where(['id_marketing' => $this->input('id_marketing'), 'periode_bulan' => $this->input('periode_bulan'), 'periode_tahun' => $this->input('periode_tahun')])->value('id');
        return [
            'id_marketing' => [
                'required', 'exists:users,id',
                Rule::unique('marketing_target', 'id_marketing')->where(fn ($query) => $query
                    ->where('periode_bulan', $this->input('periode_bulan'))
                    ->where('periode_tahun', $this->input('periode_tahun')))->ignore($targetId),
            ],
            'periode_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'periode_tahun' => ['required', 'integer', 'min:2024', 'max:2030'],
            'target_unit' => ['required', 'integer', 'min:1'],
        ];
    }
}

