<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSistem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PengaturanController extends Controller
{
    public function index(): View
    {
        $keys = [
            'default_kpr_bunga',
            'default_cash_keras_diskon',
            'dp_subsidi_min_persen',
            'dp_subsidi_max_persen',
            'dp_non_subsidi_min_persen',
            'dp_non_subsidi_max_persen',
        ];

        $settings = PengaturanSistem::getValues($keys);

        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_kpr_bunga' => ['required', 'numeric', 'min:0', 'max:50'],
            'default_cash_keras_diskon' => ['required', 'numeric', 'min:0', 'max:100'],
            'dp_subsidi_min_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'dp_subsidi_max_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'dp_non_subsidi_min_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'dp_non_subsidi_max_persen' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($validated as $key => $value) {
            PengaturanSistem::updateOrCreate(
                ['kunci' => $key],
                ['nilai' => (string) $value],
            );
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
