<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

use Knp\Snappy\Pdf;

class PdfService
{
    // src/Service/PdfService.php

    private $pdf;

    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    public function generatePdf($html)
    {
        return $this->pdf->getOutputFromHtml($html);
    }
}
