<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    private const MAX_RESULTS = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function createQuery(): QueryBuilder {
        $dql = $this->createQueryBuilder('task');

        $dql->orderBy('task.createdAt', 'DESC');

        return $dql;
    }

    /**
     * @param int $page
     * @param array $filters
     * @return Paginator|Task[]
     */
    public function pagination(int $page = 1, array $filters = []): Paginator {
        $dql = $this->createQuery();

        $this->filter($dql, $filters);

        $dql->setMaxResults(self::MAX_RESULTS)
            ->setFirstResult(($page - 1) * self::MAX_RESULTS);

        $query = $dql->getQuery();

        return new Paginator($query);
    }

    private function filter(QueryBuilder &$dql, array $filters = []) {

        if(!empty($filters['label'])) {
            $dql->andWhere('LOWER(task.label) LIKE LOWER(:label)')
                ->setParameter('label', '%'.$filters['label'].'%');
        }

        if(!empty($filters['user'])) {
            $dql->andWhere('task.user = :user')
                ->setParameter(':user', $filters['user'])
            ;
        }

        if(!empty($filters['customer'])) {
            $dql->andWhere('task.customer = :customer')
                ->setParameter(':customer', $filters['customer'])
            ;
        }

        if(isset($filters['closed'])) {
            $dql->andWhere('task.closed = :closed')
                ->setParameter(':closed', $filters['closed'])
            ;
        }
    }
}