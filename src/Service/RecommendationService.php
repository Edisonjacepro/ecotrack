<?php

namespace App\Service;

use App\Entity\EcoAction;
use App\Repository\EcoActionRepository;

final class RecommendationService
{
    public function __construct(private EcoActionRepository $ecoActionRepository)
    {
    }

    /**
     * @return EcoAction[]
     */
    public function recommend(string $category, int $limit = 5): array
    {
        return $this->ecoActionRepository->findActiveByCategory($category, $limit);
    }
}