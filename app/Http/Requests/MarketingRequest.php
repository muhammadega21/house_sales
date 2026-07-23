<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MarketingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $marketing = $this->route('marketing');
        $marketingId = $marketing instanceof User ? $marketing->id : $marketing;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($marketingId)],
            'password' => $isUpdate ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($marketingId)],
            'foto_profil' => ['nullable', 'image', 'max:5120'],
            'persentase_komisi' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', Rule::in(['aktif', 'non_aktif'])],
        ];
    }
}
