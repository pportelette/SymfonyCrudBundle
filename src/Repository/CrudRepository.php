<?php

namespace Pportelette\CrudBundle\Repository;

use Pportelette\PageableBundle\Repository\AbstractRepository;
use Pportelette\PageableBundle\Model\Pageable;
use Doctrine\Persistence\ManagerRegistry;

class CrudRepository extends AbstractRepository implements CrudRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, string $class)
    {
        parent::__construct($registry, $class);
    }

    public function getAll(int $page, array $filters = []): Pageable {
        $qb = $this->createQueryBuilder('w');

        foreach($filters as $prop => $value) {
            $qb->andWhere($qb->expr()->like('w.'.$prop, ':'.$prop));
            $qb->setParameter($prop, '%'.$value.'%');
        }

        return $this->getPage(
            $qb,
            $page
        );
    }

    public function getList(?array $filters = null): array {
        $qb = $this->createQueryBuilder('w');
        
        foreach($filters as $prop => $value) {
            $qb->andWhere($qb->expr()->like('w.'.$prop, ':'.$prop));
            $qb->setParameter($prop, '%'.$value.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
