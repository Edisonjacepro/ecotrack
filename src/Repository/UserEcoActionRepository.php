<?php

namespace App\Repository;

use App\Entity\UserEcoAction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserEcoAction>
 */
class UserEcoActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEcoAction::class);
    }

    /**
     * @return UserEcoAction[]
     */
    public function findActiveForUser(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('userAction')
            ->andWhere('userAction.user = :user')
            ->andWhere('userAction.status != :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'done')
            ->orderBy('userAction.startedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
