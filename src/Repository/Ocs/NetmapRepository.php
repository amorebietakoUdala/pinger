<?php

namespace App\Repository\Ocs;

use App\Entity\Ocs\Hardware;
use App\Entity\Ocs\Netmap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Netmap>
 */
class NetmapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Netmap::class);
    }

    public function findAll(): array {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin(Hardware::class,'h', Join::WITH, 'n.name = h.')
            ->andWhere('n.ip = h.ipaddr')
            ->orderBy('n.ip', 'ASC')
           ;
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Netmap[] Returns an array of Netmap objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Netmap
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
