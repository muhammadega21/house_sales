<?php

declare(strict_types=1);

namespace App\Services;

final class PdfService
{
    public function downloadView(string $view, array $data, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $dompdf = app('dompdf.wrapper');
        $dompdf->loadView($view, $data);

        return $dompdf
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }
}
