<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @param int $page
     * @return Paginator|Customer[]
     */
    public function pagination(int $page = 1): Paginator {
        $dql = $this->createQueryBuilder('customer');

        $dql
            ->select(['customer', 'tasks'])
            ->leftJoin('customer.tasks', 'tasks')
        ;

        $dql->orderBy('customer.label', 'ASC');

        $query = $dql->getQuery();
        $query->setMaxResults(10);
        $query->setFirstResult(($page - 1) * 10);

        return new Paginator($query);
    }
}
