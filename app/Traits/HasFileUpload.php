<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasFileUpload
{
    /**
     * Upload a file with validation and remove old file if exists.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $oldFile
     * @return string Path to the uploaded file relative to 'public' disk
     * @throws ValidationException
     */
    public function uploadFile(UploadedFile $file, string $folder, ?string $oldFile = null): string
    {
        $mimeType = $file->getMimeType();
        $isPdf = $mimeType === 'application/pdf';
        
        // 5MB limit for images, 10MB limit for pdfs (in kilobytes)
        $maxSize = $isPdf ? 10240 : 5120;

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:' . $maxSize,
                ]
            ],
            [
                'file.max' => $isPdf 
                    ? 'Ukuran file PDF tidak boleh melebihi 10MB.' 
                    : 'Ukuran file gambar tidak boleh melebihi 5MB.',
                'file.mimes' => 'Format file harus berupa PDF, JPG, JPEG, atau PNG.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Delete old file if provided
        if ($oldFile) {
            $this->deleteFile($oldFile);
        }

        // Stores under storage/app/public/folder/
        return $file->store($folder, 'public');
    }

    /**
     * Delete a file from public storage if it exists.
     *
     * @param string|null $path
     * @return void
     */
    public function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
