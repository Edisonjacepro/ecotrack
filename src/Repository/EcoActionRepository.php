<?php

namespace App\Repository;

use App\Entity\EcoAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EcoAction>
 */
class EcoActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcoAction::class);
    }

    /**
     * @return EcoAction[]
     */
    public function findActiveByCategory(string $category, int $limit = 10): array
    {
        return $this->createQueryBuilder('action')
            ->andWhere('action.category = :category')
            ->andWhere('action.active = true')
            ->setParameter('category', $category)
            ->setMaxResults($limit)
            ->orderBy('action.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
