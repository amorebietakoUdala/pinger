<?php

namespace App\Repository\Ocs;

use App\Entity\Ocs\Subnet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subnet>
 */
class SubnetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subnet::class);
    }

    public function findByTag(string $filter): array {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.tag = :filter')
            ->setParameter('filter', $filter)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    //    /**
    //     * @return Subnet[] Returns an array of Subnet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Subnet
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    
}
