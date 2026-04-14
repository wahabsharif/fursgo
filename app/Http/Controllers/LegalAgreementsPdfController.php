<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class LegalAgreementsPdfController extends Controller
{
    public function __invoke()
    {
        $logoPath = public_path('images/logo/logo.svg');
        $logoDataUri = '';
        if (is_readable($logoPath)) {
            $logoDataUri = 'data:image/svg+xml;base64,'.base64_encode((string) file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('pdf.legal-agreements', [
            'logoDataUri' => $logoDataUri,
        ]);

        $filename = 'Fursgo-Legal-Agreements.pdf';
        $output = $pdf->output();

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($output),
            'Cache-Control' => 'private, must-revalidate, max-age=0',
        ]);
    }
}
