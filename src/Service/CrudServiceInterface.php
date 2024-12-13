<?php

namespace Pportelette\CrudBundle\Service;

use Pportelette\PageableBundle\Model\Pageable;
use Pportelette\CrudBundle\Model\ViewModel;
use Pportelette\CrudBundle\Repository\CrudRepositoryInterface;

interface CrudServiceInterface {
    public function setRepository(CrudRepositoryInterface $repository): void;
    public function setEntityClass(string $entityClass): void;
    
    public function getAll(int $page, array $filters = []): Pageable;
    public function getList(array $filters = []): array;
    public function getEntity(string $id): ViewModel;
    public function createEntity(array $properties): ViewModel;
    public function updateEntity(string $id, array $properties): ViewModel;
    public function deleteEntity(string $id): void;
}