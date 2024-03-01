<?php

namespace Pportelette\CrudBundle\Repository;

use Pportelette\PageableBundle\Model\Pageable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CrudRepositoryInterface extends ServiceEntityRepositoryInterface  {
    public function getAll(int $page, array $filters = []): Pageable;
    public function getList(array $filters = []): array;
}