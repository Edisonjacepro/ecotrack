<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\CarbonRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/report/summary', name: 'api_report_summary', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
class ReportController extends AbstractController
{
    public function __invoke(CarbonRecordRepository $carbonRecordRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $startOfMonth = new \DateTimeImmutable('first day of this month midnight');
        $monthlyTotal = $carbonRecordRepository->getTotalForUser($user, $startOfMonth);
        $categoryTotals = $carbonRecordRepository->getTotalsByCategoryForUser($user, $startOfMonth);
        $monthlyTrend = $carbonRecordRepository->getMonthlyTotalsForUser($user, 6);

        return $this->json([
            'monthly_total' => $monthlyTotal,
            'category_totals' => $categoryTotals,
            'monthly_trend' => $monthlyTrend,
        ]);
    }
}
