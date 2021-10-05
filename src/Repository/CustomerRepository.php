<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    private const MAX_RESULTS = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    private function createQuery(): QueryBuilder {
        return $this->createQueryBuilder('customer')
            ->orderBy('customer.label', 'ASC');
    }

    /**
     * @param int $page
     * @return Paginator|Customer[]
     */
    public function pagination(int $page = 1): Paginator {
        $dql = $this->createQuery();

        $query = $dql->getQuery()
            ->setMaxResults(self::MAX_RESULTS)
            ->setFirstResult(($page - 1) * self::MAX_RESULTS);

        return new Paginator($query);
    }
}
