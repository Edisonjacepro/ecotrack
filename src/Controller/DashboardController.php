<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CarbonRecordRepository;
use App\Repository\UserEcoActionRepository;
use App\Service\RecommendationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        CarbonRecordRepository $carbonRecordRepository,
        UserEcoActionRepository $userEcoActionRepository,
        RecommendationService $recommendationService
    ): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $startOfMonth = new \DateTimeImmutable('first day of this month midnight');
        $monthlyTotal = $carbonRecordRepository->getTotalForUser($user, $startOfMonth);
        $categoryTotals = $carbonRecordRepository->getTotalsByCategoryForUser($user, $startOfMonth);
        $monthlyTrend = $carbonRecordRepository->getMonthlyTotalsForUser($user, 6);
        $activeActions = $userEcoActionRepository->findActiveForUser($user, 5);

        $topCategory = $categoryTotals[0]['category'] ?? null;
        $topCategoryTotal = $categoryTotals[0]['total'] ?? null;
        $recommendedActions = $topCategory ? $recommendationService->recommend($topCategory, 4) : [];

        return $this->render('dashboard/index.html.twig', [
            'monthly_total' => $monthlyTotal,
            'category_totals' => $categoryTotals,
            'monthly_trend' => $monthlyTrend,
            'active_actions' => $activeActions,
            'active_actions_count' => count($activeActions),
            'top_category' => $topCategory,
            'top_category_total' => $topCategoryTotal,
            'recommended_actions' => $recommendedActions,
        ]);
    }
}
