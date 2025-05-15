<?php

namespace App\Repository\Ocs;

use App\Entity\Ocs\Hardware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hardware>
 */
class HardwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Hardware::class);
    }

    public function findAll() : array {
        $qb = $this->findAllQB();
        return $qb->getQuery()->getResult();
    }

    public function findAllQB(): QueryBuilder {
        $qb = $this->createQueryBuilder('h')
            ->andWhere('h.deviceid != \'_SYSTEMGROUP_\'')
            ->orderBy('h.name', 'ASC');
        return $qb;
    }

    public function findBetween(string $start, string $end): array {
        $qb = $this->findBetweenQB($start, $end);
        return $qb->getQuery()->getResult();    
    }

    public function findOneByIp(string $ip): ?Hardware {
        $qb = $this->createQueryBuilder('h');
        $qb = $this->andWhereIpAddressEquals($qb, $ip);
        $result = $qb->getQuery()->getResult();
        if ( count($result) > 0 ) {
            return $result[0];
        }
        return null;
    }

    public function findBetweenQB(string $start, string $end): QueryBuilder {
        $qb = $this->findAllQB();
        $qb = $this->andWhereIpAddressBetween($qb, $start, $end);
        $qb = $this->andWhereArchive($qb, false);
        return $qb;
    }

    public function findByArchiveQB(bool $archive): QueryBuilder {
        $qb = $this->findAllQB();
        $qb = $this->andWhereArchive($qb, $archive);
        return $qb;
    }

    private function andWhereArchive(QueryBuilder $qb, bool $archive): QueryBuilder {
        $qb = $qb->andWhere('h.archive = :archive')
                 ->setParameter('archive', $archive);
        return $qb;
    }

    private function andWhereIpAddressEquals(QueryBuilder $qb, string $ip, ): QueryBuilder {
        $qb = $qb->andWhere('h.ipaddr = :start')
                ->setParameter('start', $ip)
                ->orderBy('h.lastdate', 'DESC');
        return $qb;
    }

    private function andWhereIpAddressBetween(QueryBuilder $qb, ?string $start, ?string $end): QueryBuilder {
        if( null !== $start ) {
            $qb = $qb->andWhere('h.ipaddr >= :start')
                 ->setParameter('start', $start);
        }
        if( null !== $start ) {
            $qb =  $qb->andWhere('h.ipaddr <= :end')
                ->setParameter('end', $end);
        }
        return $qb;
    }

    private function andWhereNameBeetween(QueryBuilder $qb, ?string $start, ?string $end): QueryBuilder {
        if( null !== $start ) {
            $qb = $qb->andWhere('h.name >= :start')
            ->setParameter('start', $start);
        }
        if( null !== $end ) {
            $qb = $qb->andWhere('h.name <= :end')
                ->setParameter('end', $end);
        }
        return $qb;
    }

    public function findByNameBetweenQB ($start, $end): QueryBuilder {
        $qb = $this->findAllQB();
        $qb = $this->andWhereNameBeetween($qb, $start, $end);
        return $qb;
    }

    public function findByNameBetween ($start, $end): array {
        $qb = $this->findByNameBetweenQB($start, $end);
    
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Hardware[] Returns an array of Hardware objects
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

    //    public function findOneBySomeField($value): ?Hardware
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
