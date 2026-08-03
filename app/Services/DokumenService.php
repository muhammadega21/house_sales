<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisDokumen;
use App\Enums\StatusVerifikasiDokumen;
use App\Models\DokumenKpr;
use App\Models\Konsumen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DokumenService extends BaseService
{
    public function upload(array $data): DokumenKpr
    {
        return DB::transaction(function () use ($data): DokumenKpr {
            $userId = auth()->id();
            if (!$userId) {
                throw ValidationException::withMessages(['auth' => 'Silakan login terlebih dahulu.']);
            }

            $konsumen = Konsumen::findOrFail($data['id_konsumen']);
            if ((int) $konsumen->id_marketing !== (int) $userId) {
                throw ValidationException::withMessages(['id_konsumen' => 'Anda hanya dapat mengelola dokumen konsumen milik Anda.']);
            }

            $jenis = $data['jenis_dokumen'];
            $jenisEnum = JenisDokumen::tryFrom($jenis);
            if (!$jenisEnum) {
                throw ValidationException::withMessages(['jenis_dokumen' => 'Jenis dokumen tidak valid.']);
            }

            if ($jenis !== 'lainnya') {
                $existing = DokumenKpr::where('id_konsumen', $konsumen->id)
                    ->where('jenis_dokumen', $jenis)
                    ->exists();
                if ($existing) {
                    throw ValidationException::withMessages(['jenis_dokumen' => 'Dokumen dengan jenis ini sudah pernah diupload untuk konsumen ini.']);
                }
            }

            $file = $data['file_dokumen'];
            if (!$file instanceof UploadedFile) {
                throw ValidationException::withMessages(['file_dokumen' => 'File dokumen wajib diupload.']);
            }

            $validator = Validator::make(
                ['file_dokumen' => $file, 'jenis_dokumen' => $jenis],
                [
                    'file_dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . $jenisEnum->maxsize()],
                    'jenis_dokumen' => ['required', 'in:' . implode(',', array_map(fn(JenisDokumen $item): string => $item->value, JenisDokumen::cases()))],
                ],
                [
                    'file_dokumen.max' => $this->messageForMaxSize($jenisEnum),
                    'file_dokumen.mimes' => 'Format file harus berupa PDF, JPG, JPEG, atau PNG.',
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $relativePath = $this->storeFile($file, $konsumen->id, $jenis);
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $document = DokumenKpr::create([
                'id_konsumen' => $konsumen->id,
                'jenis_dokumen' => $jenis,
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $relativePath,
                'ukuran_file' => $file->getSize(),
                'tipe_file' => $this->normalizeType($file, $extension),
                'status_verifikasi' => StatusVerifikasiDokumen::BelumDiverifikasi->value,
                'diupload_oleh' => $userId,
            ]);

            return $document;
        });
    }

    public function replace(int $idDokumen, UploadedFile $fileBaru): DokumenKpr
    {
        return DB::transaction(function () use ($idDokumen, $fileBaru): DokumenKpr {
            $document = DokumenKpr::findOrFail($idDokumen);
            $this->ensureOwnership($document->konsumen);

            $jenisEnum = JenisDokumen::tryFrom($document->jenis_dokumen);
            if (!$jenisEnum) {
                throw ValidationException::withMessages(['jenis_dokumen' => 'Jenis dokumen tidak valid.']);
            }

            $validator = Validator::make(
                ['file_dokumen' => $fileBaru],
                ['file_dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . $jenisEnum->maxsize()]],
                ['file_dokumen.max' => $this->messageForMaxSize($jenisEnum)]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($document->path_file) {
                Storage::disk('public')->delete($document->path_file);
            }

            $relativePath = $this->storeFile($fileBaru, $document->id_konsumen, $document->jenis_dokumen);
            $document->update([
                'nama_file' => $fileBaru->getClientOriginalName(),
                'path_file' => $relativePath,
                'ukuran_file' => $fileBaru->getSize(),
                'tipe_file' => $this->normalizeType($fileBaru, strtolower($fileBaru->getClientOriginalExtension() ?: $fileBaru->extension())),
                'status_verifikasi' => StatusVerifikasiDokumen::BelumDiverifikasi->value,
                'catatan_verifikasi' => null,
                'tanggal_verifikasi' => null,
            ]);

            return $document->fresh();
        });
    }

    public function deleteDocument(int $idDokumen): bool
    {
        return DB::transaction(function () use ($idDokumen): bool {
            $document = DokumenKpr::findOrFail($idDokumen);
            $this->ensureOwnership($document->konsumen);

            if (!in_array($document->status_verifikasi, [StatusVerifikasiDokumen::BelumDiverifikasi->value, StatusVerifikasiDokumen::PerluRevisi->value], true)) {
                throw ValidationException::withMessages(['status_verifikasi' => 'Dokumen yang sudah diverifikasi tidak dapat dihapus.']);
            }

            if ($document->path_file) {
                Storage::disk('public')->delete($document->path_file);
            }

            return (bool) $document->delete();
        });
    }

    public function getForKonsumen(int $idKonsumen): \Illuminate\Database\Eloquent\Collection
    {
        return DokumenKpr::where('id_konsumen', $idKonsumen)
            ->orderByDesc('tanggal_upload')
            ->get();
    }

    public function getChecklist(int $idKonsumen): array
    {
        $documents = $this->getForKonsumen($idKonsumen)->keyBy('jenis_dokumen');
        $checklist = [];

        foreach (JenisDokumen::cases() as $jenis) {
            if (!$jenis->wajib()) {
                continue;
            }

            $document = $documents->get($jenis->value);
            $status = $document?->status_verifikasi;
            $checklist[] = [
                'jenis' => $jenis->value,
                'label' => $jenis->label(),
                'wajib' => true,
                'uploaded' => (bool) $document,
                'status_verifikasi' => $status,
                'is_valid' => $status === StatusVerifikasiDokumen::Valid->value,
                'needs_revision' => $status === StatusVerifikasiDokumen::PerluRevisi->value,
                'document' => $document,
            ];
        }

        return $checklist;
    }

    public function isComplete(int $idKonsumen): bool
    {
        foreach ($this->getChecklist($idKonsumen) as $item) {
            if (!$item['is_valid']) {
                return false;
            }
        }

        return true;
    }

    public function getMissingDocuments(int $idKonsumen): array
    {
        $missing = [];
        foreach ($this->getChecklist($idKonsumen) as $item) {
            if (!$item['uploaded']) {
                $missing[] = $item['jenis'];
            }
        }

        return $missing;
    }

    private function storeFile(UploadedFile $file, int $konsumenId, string $jenis): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $timestamp = now()->format('YmdHis');
        $filename = sprintf('%s_%s_%s.%s', Str::slug($jenis, '_'), $konsumenId, $timestamp, $extension);
        $path = 'dokumen-kpr/' . $konsumenId;
        $file->storeAs($path, $filename, 'public');

        return $path . '/' . $filename;
    }

    private function ensureOwnership(?Konsumen $konsumen): void
    {
        $userId = auth()->id();
        if (!$userId || !$konsumen || (int) $konsumen->id_marketing !== (int) $userId) {
            throw ValidationException::withMessages(['id_konsumen' => 'Anda tidak memiliki akses ke dokumen ini.']);
        }
    }

    private function messageForMaxSize(JenisDokumen $jenis): string
    {
        if (in_array($jenis->value, ['slip_gaji', 'rekening_koran', 'lainnya'], true)) {
            return 'Ukuran file PDF tidak boleh melebihi 10MB.';
        }

        return 'Ukuran file gambar tidak boleh melebihi 5MB.';
    }

    private function normalizeType(UploadedFile $file, string $extension): string
    {
        if (in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return $extension;
        }

        return $file->getMimeType() === 'application/pdf' ? 'pdf' : $extension;
    }
}
