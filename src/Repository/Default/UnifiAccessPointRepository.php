<?php

namespace App\Repository\Default;

use App\Entity\Default\UnifiAccessPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UnifiAccessPoint>
 */
class UnifiAccessPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnifiAccessPoint::class);
    }


    public function findByStateAndPingStatus($state = 'Offline', $pingStatus = 'unreachable'): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orWhere('c.state = :state')
            ->setParameter('state', $state)
            ->orWhere('c.pingStatus = :pingStatus')
            ->setParameter('pingStatus', $pingStatus);
        $qb = $this->andWhereNotExcludedFormReport($qb, 'excludeFromReport');
        $qb->orderBy('c.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

    private function andWhereNotExcludedFormReport(QueryBuilder $qb, string $excludeFromReport): QueryBuilder
    {
        $qb = $qb->andWhere('c.excludeFromReport is null or c.excludeFromReport = :excludeFromReport')
            ->setParameter('excludeFromReport', $excludeFromReport);
        return $qb;
    }

    //    /**
    //     * @return UnifiAccessPoint[] Returns an array of UnifiAccessPoint objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UnifiAccessPoint
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
