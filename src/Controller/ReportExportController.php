<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CarbonRecordRepository;
use App\Repository\UserEcoActionRepository;
use App\Service\PdfReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportExportController extends AbstractController
{
    #[Route('/report/pdf', name: 'app_report_pdf')]
    public function pdf(
        CarbonRecordRepository $carbonRecordRepository,
        UserEcoActionRepository $userEcoActionRepository,
        PdfReportService $pdfReportService
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $startOfMonth = new \DateTimeImmutable('first day of this month midnight');
        $summary = [
            'monthly_total' => $carbonRecordRepository->getTotalForUser($user, $startOfMonth),
            'category_totals' => $carbonRecordRepository->getTotalsByCategoryForUser($user, $startOfMonth),
            'monthly_trend' => $carbonRecordRepository->getMonthlyTotalsForUser($user, 6),
        ];

        $actions = $userEcoActionRepository->findActiveForUser($user, 10);

        $pdf = $pdfReportService->generateDashboardPdf($summary, $actions);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ecotrack-report.pdf"',
        ]);
    }
}
