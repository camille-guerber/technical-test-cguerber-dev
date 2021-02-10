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
     * @var QueryBuilder
     */
    private QueryBuilder $dql;

    /**
     * @var int
     */
    private const MAX_RESULTS = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
        $this->dql = $this
            ->createQueryBuilder('task')
            ->select(['task', 'users', 'customers'])
            ->leftJoin('task.user', 'users')
            ->leftJoin('task.customer', 'customers')
            ->orderBy('task.createdAt', 'DESC')
        ;
    }

    /**
     * @param int $page
     * @return Paginator|Task[]
     */
    public function pagination(int $page = 1): Paginator {

        return new Paginator(
            $this->dql
                ->getQuery()
                ->setMaxResults(self::MAX_RESULTS)
                ->setFirstResult(($page - 1) * self::MAX_RESULTS)
        );
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function getOpenedTasks(int $page = 1): Paginator {

        return new Paginator(
            $this->dql
                ->andWhere($this->dql->expr()->eq('task.closed', ':propertyCheck'))
                ->setParameter(':propertyCheck', false)
                ->getQuery()
                ->setMaxResults(self::MAX_RESULTS)
                ->setFirstResult(($page - 1) * self::MAX_RESULTS)
        );
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function getUnassignedTasks(int $page = 1): Paginator {

        return new Paginator(
            $this->dql
                ->andWhere('task.user IS NULL')
                ->getQuery()
                ->setMaxResults(self::MAX_RESULTS)
                ->setFirstResult(($page - 1) * self::MAX_RESULTS)
        );
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function getOwnedOpenedTasks(int $page = 1): Paginator {

        return new Paginator(
            $this->dql
                ->where('task.user IS NOT NULL')
                ->andWhere($this->dql->expr()->eq('task.closed', ':propertyCheck'))
                ->setParameter(':propertyCheck', false)
                ->getQuery()
                ->setMaxResults(self::MAX_RESULTS)
                ->setFirstResult(($page - 1) * self::MAX_RESULTS)
        );
    }

    public function getFilteredTasks(int $page = 1, $filters = null, $unmapped = null) {

        if($filters->getLabel()) {
            $this->dql
                ->andWhere('task.label LIKE :label')
                ->setParameter('label', '%'.$filters->getLabel().'%')
            ;
        }

        if($filters->getUser()) {
            $this->dql
                ->andWhere('task.user = :user')
                ->setParameter(':user', $filters->getUser())
            ;
        }

        if($filters->getCustomer()) {
            $this->dql
                ->andWhere('task.customer = :customer')
                ->setParameter(':customer', $filters->getCustomer())
            ;
        }

        if(isset($unmapped)) {
            $this->dql
                ->andWhere($this->dql->expr()->eq('task.closed', ':propertyCheck'))
                ->setParameter(':propertyCheck', $unmapped)
            ;
        }

        return new Paginator(
            $this->dql
                ->getQuery()
                ->setMaxResults(self::MAX_RESULTS)
                ->setFirstResult(($page - 1) * self::MAX_RESULTS)
        );
    }

    /**
     * @param User $user
     * @param int $page
     * @return Paginator
     */
    public function getUserTasks(User $user, int $page = 1) {

        return new Paginator(
            $this->dql
                ->andWhere('task.user = :user')
                ->setParameter(':user', $user)
                ->getQuery()
                ->setMaxResults(self::MAX_RESULTS)
                ->setFirstResult(($page - 1) * self::MAX_RESULTS)
        );
    }
}