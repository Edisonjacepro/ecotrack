<?php

namespace App\Repository;

use App\Entity\CarbonRecord;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarbonRecord>
 */
class CarbonRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarbonRecord::class);
    }

    public function getTotalForUser(User $user, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): float
    {
        $qb = $this->createQueryBuilder('record')
            ->select('COALESCE(SUM(record.amountKg), 0) as total')
            ->andWhere('record.user = :user')
            ->setParameter('user', $user);

        if ($from) {
            $qb->andWhere('record.recordedAt >= :from')
                ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('record.recordedAt <= :to')
                ->setParameter('to', $to);
        }

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<int, array{category: string, total: float}>
     */
    public function getTotalsByCategoryForUser(User $user, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $qb = $this->createQueryBuilder('record')
            ->select('record.category as category, COALESCE(SUM(record.amountKg), 0) as total')
            ->andWhere('record.user = :user')
            ->setParameter('user', $user)
            ->groupBy('record.category')
            ->orderBy('total', 'DESC');

        if ($from) {
            $qb->andWhere('record.recordedAt >= :from')
                ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('record.recordedAt <= :to')
                ->setParameter('to', $to);
        }

        return array_map(
            static fn (array $row) => ['category' => $row['category'], 'total' => (float) $row['total']],
            $qb->getQuery()->getArrayResult()
        );
    }

    /**
     * @return array<int, array{month: string, total: float}>
     */
    public function getMonthlyTotalsForUser(User $user, int $months = 6): array
    {
        $start = (new \DateTimeImmutable('first day of this month midnight'))
            ->modify(sprintf('-%d months', max(0, $months - 1)));

        $sql = <<<'SQL'
            SELECT TO_CHAR(DATE_TRUNC('month', recorded_at), 'YYYY-MM') AS month,
                   COALESCE(SUM(amount_kg), 0) AS total
            FROM carbon_record
            WHERE user_id = :userId
              AND recorded_at >= :start
            GROUP BY month
            ORDER BY month ASC
        SQL;

        $connection = $this->getEntityManager()->getConnection();
        $rows = $connection->fetchAllAssociative($sql, [
            'userId' => $user->getId(),
            'start' => $start->format('Y-m-d H:i:s'),
        ]);

        return array_map(
            static fn (array $row) => ['month' => $row['month'], 'total' => (float) $row['total']],
            $rows
        );
    }

    /**
     * @return CarbonRecord[]
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('record')
            ->andWhere('record.user = :user')
            ->setParameter('user', $user)
            ->orderBy('record.recordedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
