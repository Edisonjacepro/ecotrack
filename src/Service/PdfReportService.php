<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

final class PdfReportService
{
    public function __construct(private Environment $twig)
    {
    }

    /**
     * @param array<string, mixed> $summary
     * @param array<int, mixed> $actions
     */
    public function generateDashboardPdf(array $summary, array $actions): string
    {
        $html = $this->twig->render('report/pdf.html.twig', [
            'summary' => $summary,
            'actions' => $actions,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}