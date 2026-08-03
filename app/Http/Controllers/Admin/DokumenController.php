<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenKpr;
use App\Models\Konsumen;
use App\Services\DokumenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DokumenController extends Controller
{
    public function __construct(private readonly DokumenService $dokumenService) {}

    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $filterStatus = $request->input('status_verifikasi', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = DokumenKpr::query()
            ->with(['konsumen', 'konsumen.marketing'])
            ->orderByDesc('created_at');

        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('konsumen', function ($q) use ($term) {
                    $q->where('nama_lengkap', 'like', $term)
                        ->orWhere('nik', 'like', $term);
                })->orWhere('nama_file', 'like', $term);
            });
        }

        if ($filterStatus !== '') {
            $query->where('status_verifikasi', $filterStatus);
        }

        $documents = $query->paginate($perPage)->withQueryString();

        return view('admin.dokumen.index', compact('documents', 'search', 'filterStatus', 'perPage'));
    }

    public function verifikasi(int $id): View
    {
        $document = DokumenKpr::with(['konsumen', 'konsumen.marketing'])->findOrFail($id);

        return view('admin.dokumen.verifikasi', compact('document'));
    }

    public function prosesVerifikasi(Request $request, int $id): RedirectResponse
    {
        $document = DokumenKpr::findOrFail($id);

        $request->validate([
            'status_verifikasi' => ['required', 'in:valid,tidak_valid'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $document->update([
            'status_verifikasi' => $request->input('status_verifikasi'),
            'catatan_verifikasi' => $request->input('catatan'),
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_at' => now(),
        ]);

        $message = $request->input('status_verifikasi') === 'valid'
            ? 'Dokumen berhasil diverifikasi.'
            : 'Dokumen ditolak.';

        return redirect()->route('admin.dokumen.index')->with('success', $message);
    }
}