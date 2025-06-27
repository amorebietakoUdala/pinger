<?php

namespace App\Repository\Default;

use App\Entity\Default\Computer;
use App\Entity\Default\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Computer>
 */
class ComputerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Computer::class);
    }

    public function findOneByHostname($value): ?Computer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.hostname = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByFilter(array $filter): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($filter['hostname'] !== null) {
            $qb = $this->andWhereHostnameLike($qb, $filter['hostname']);
        }

        if ($filter['mac'] !== null) {
            $qb = $this->andWhereMacLike($qb, $filter['mac']);
        }

        if ($filter['origin'] !== null) {
            $qb = $this->andWhereOrigin($qb, $filter['origin']);
        }

        $qb = $this->addOrderByIp($qb, 'ASC');

        $result = $qb->getQuery()->getResult();

        return $this->filterIps($result, $filter['startIp'], $filter['endIp']);
    }

    private function addOrderByIp(QueryBuilder $qb, string $order)
    {
        $qb = $qb->addOrderBy('c.ip', $order);
        return $qb;
    }

    private function andWhereHostnameLike(QueryBuilder &$qb, string $hostname): QueryBuilder
    {
        $qb = $qb->andWhere('c.hostname LIKE :hostname')
            ->setParameter('hostname', "%$hostname%");
        return $qb;
    }

    private function filterIps($result, ?string $startIp, ?string $endIp): array
    {

        if (empty($startIp) || empty($endIp)) {
            return $result;
        }


        if (ip2long($startIp) === false || ip2long($endIp) === false) {
            throw new \InvalidArgumentException('Las IPs proporcionadas no son válidas.');
        }

        return array_filter($result, function ($computer) use ($startIp, $endIp) {
            $computerIp = ip2long($computer->getIp());
            return $computerIp >= ip2long($startIp) && $computerIp <= ip2long($endIp);
        });
    }

    private function andWhereMacLike(QueryBuilder $qb, string $mac): QueryBuilder
    {
        $qb = $qb->andWhere('c.mac LIKE :mac')
            ->setParameter('mac', "%$mac%");
        return $qb;
    }
    private function andWhereOrigin(QueryBuilder $qb, string $origin): QueryBuilder
    {
        $qb = $qb->andWhere('c.origin = :origin')
            ->setParameter('origin', $origin);
        return $qb;
    }
       
    public function leftJoinVendor(QueryBuilder $qb)
    {
        $qb = $qb->leftJoin(Vendor::class, 'v',  'WITH', 'c.mac like CONCAT(v.prefix,\'%\')');

        return $qb;
    }

    public function getComputerByDay(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.lastSucessfullPing >= :today')
            ->setParameter('today', $today)
            ->andWhere('c.necessary is null or c.necessary = false')
            ->orderBy('c.hostname', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getComputerByDayWithoutHost(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.lastSucessfullPing >= :today')
            ->setParameter('today', $today)
            ->andWhere('c.hostname IS NULL');

        return $qb->getQuery()->getResult();
    }

    public function getDuplicatedComputers(): array
    {
        $subQb = $this->createQueryBuilder('c')
            ->select('c.hostname')
            ->groupBy('c.hostname')
            ->having('COUNT(c.id) > 1');

        $hostnames = array_map(fn($row) => $row['hostname'], $subQb->getQuery()->getArrayResult());

        if (empty($hostnames)) {
            return [];
        }
        return $this->createQueryBuilder('c')
            ->where('c.hostname IN (:hostnames)')
            ->setParameter('hostnames', $hostnames)
            ->getQuery()
            ->getResult();
    }
    public function findbetweenStartAndEndip(string $startIp, string $endIp): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.ip BETWEEN :startip AND :endip')
            ->setParameter('startip', $startIp)
            ->setParameter('endip', $endIp)
            ->getQuery()
            ->getResult();
    }

    public function getNextNumberFromDatabase(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT MAX(CAST(SUBSTRING(hostname, 2, 5) AS UNSIGNED)) AS max_num
        FROM computer
        WHERE hostname REGEXP '^[A-Za-z][0-9]{5}$'";

        $result = $conn->fetchAssociative($sql);

        return $result['max_num'] !== null ? ((int)$result['max_num'] + 1) : 1;
    }
}
