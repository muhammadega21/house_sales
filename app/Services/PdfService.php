<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

final class PdfService
{
    public function downloadView(string $view, array $data, string $filename): StreamedResponse
    {
        $dompdf = app('dompdf.wrapper');
        $dompdf->loadView($view, $data);

        return $dompdf
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }
}
