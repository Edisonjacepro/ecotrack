<?php

namespace App\Controller\Api;

use App\Service\CarbonCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/calculate', name: 'api_calculate', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
class CarbonCalculatorController extends AbstractController
{
    public function __invoke(Request $request, CarbonCalculatorService $calculator): JsonResponse
    {
        try {
            $payload = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return $this->json(['error' => 'Invalid JSON payload.'], 400);
        }
        $category = (string) ($payload['category'] ?? '');
        $data = (array) ($payload['data'] ?? []);

        if ($category === '') {
            return $this->json(['error' => 'Category is required.'], 400);
        }

        try {
            $amount = $calculator->calculate($category, $data);
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], 400);
        }

        return $this->json([
            'category' => $category,
            'amountKg' => round($amount, 4),
        ]);
    }
}
