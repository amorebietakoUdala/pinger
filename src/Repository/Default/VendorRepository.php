<?php

namespace App\Repository\Default;

use App\Entity\Default\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vendor>
 */
class VendorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vendor::class);
    }

    public function findOneByMac(string $mac): ?Vendor {
        $qb = $this->createQueryBuilder('v')
            ->where($this->getEntityManager()->getExpressionBuilder()->like(':mac', 'CONCAT(v.prefix, \'%\')'))
            ->setParameter('mac', $mac)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    //    /**
    //     * @return Vendor[] Returns an array of Vendor objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vendor
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
