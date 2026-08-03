<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\JenisDokumen;
use App\Http\Controllers\Controller;
use App\Http\Requests\DokumenKprRequest;
use App\Models\DokumenKpr;
use App\Models\Konsumen;
use App\Services\DokumenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

final class DokumenController extends Controller
{
    public function __construct(private readonly DokumenService $dokumenService) {}

    public function index(int $idKonsumen): View
    {
        $konsumen = Konsumen::findOrFail($idKonsumen);
        abort_unless((int) $konsumen->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke konsumen ini.');

        $documents = $this->dokumenService->getForKonsumen($idKonsumen);
        $checklist = $this->dokumenService->getChecklist($idKonsumen);
        $isComplete = $this->dokumenService->isComplete($idKonsumen);
        $missingDocuments = $this->dokumenService->getMissingDocuments($idKonsumen);
        $uploadedTypes = $documents->pluck('jenis_dokumen')->all();

        return view('marketing.dokumen.index', compact(
            'konsumen',
            'documents',
            'checklist',
            'isComplete',
            'missingDocuments',
            'uploadedTypes'
        ));
    }

    public function create(int $idKonsumen): View
    {
        $konsumen = Konsumen::findOrFail($idKonsumen);
        abort_unless((int) $konsumen->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke konsumen ini.');

        $uploadedTypes = DokumenKpr::where('id_konsumen', $idKonsumen)->pluck('jenis_dokumen')->all();
        $dokumenOptions = collect(JenisDokumen::cases())->map(function (JenisDokumen $jenis) use ($uploadedTypes): array {
            return [
                'value' => $jenis->value,
                'label' => $jenis->label() . ($jenis->wajib() ? ' (Wajib)' : ' (Opsional)'),
                'wajib' => $jenis->wajib(),
                'disabled' => in_array($jenis->value, $uploadedTypes, true) && $jenis->value !== 'lainnya',
                'keterangan' => $jenis->keterangan(),
            ];
        })->values();

        return view('marketing.dokumen.create', compact('konsumen', 'dokumenOptions'));
    }

    public function store(DokumenKprRequest $request): RedirectResponse
    {
        $document = $this->dokumenService->upload($request->validated());

        return redirect()->route('marketing.dokumen.index', $document->id_konsumen)->with('success', 'Dokumen berhasil diupload dan menunggu verifikasi admin.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $document = DokumenKpr::findOrFail($id);
        abort_unless((int) $document->konsumen?->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke dokumen ini.');

        $this->dokumenService->deleteDocument($id);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function replace(int $id): View
    {
        $document = DokumenKpr::findOrFail($id);
        abort_unless((int) $document->konsumen?->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke dokumen ini.');

        return view('marketing.dokumen.replace', compact('document'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $document = DokumenKpr::findOrFail($id);
        abort_unless((int) $document->konsumen?->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke dokumen ini.');

        $request->validate([
            'file_dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $this->dokumenService->replace($id, $request->file('file_dokumen'));

        return redirect()->route('marketing.dokumen.index', $document->id_konsumen)->with('success', 'Dokumen berhasil diperbarui dan status verifikasi dikembalikan ke belum diverifikasi.');
    }

    public function download(int $id)
    {
        $document = DokumenKpr::findOrFail($id);
        abort_unless((int) $document->konsumen?->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke dokumen ini.');

        return response()->download(storage_path('app/public/' . $document->path_file), $document->nama_file);
    }

    public function preview(int $id): View
    {
        $document = DokumenKpr::findOrFail($id);
        abort_unless((int) $document->konsumen?->id_marketing === (int) auth()->id(), 403, 'Anda tidak memiliki akses ke dokumen ini.');

        return view('marketing.dokumen.preview', compact('document'));
    }
}
