<?php

namespace Pportelette\CrudBundle\Service;

use Pportelette\CrudBundle\Repository\CrudRepositoryInterface;
use Pportelette\PageableBundle\Model\Pageable;
use Pportelette\CrudBundle\Model\ViewModel;

class CrudService implements CrudServiceInterface
{
    protected $repository;
    protected $entityClass;
    
    public function __construct(CrudRepositoryInterface $repository, ?string $entityClass = null)
    {
        $this->repository = $repository;
        $this->entityClass = $entityClass;
    }

    public function setRepository(CrudRepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    public function getAll(int $page, array $filters = []): Pageable
    {
        $entitiesPage = $this->repository->getAll($page, $filters);

        $entitiesPage->items = array_map(function ($entity) {
            $viewModel = new $this->entityClass();
            $viewModel->fromEntity($entity);
            return $viewModel->getAll();
        }, $entitiesPage->items);
        
        return $entitiesPage;
    }

    public function getList(array $filters = []): array
    {
        $entities = $this->repository->getList($filters);

        $entities = array_map(function ($word) {
            $viewModel = new $this->entityClass();
            $viewModel->fromEntity($word);
            return $viewModel->getList();
        }, $entities);
        
        return $entities;
    }

    public function getEntity(string $id): ViewModel
    {
        $entity = $this->repository->find($id);
        $viewModel = new $this->entityClass();
        $viewModel->fromEntity($entity);

        return $viewModel->getEntity();
    }

    public function createEntity(array $properties): ViewModel
    {
        $viewModel = new $this->entityClass($properties);

        $entity = $viewModel->toEntity();
        $this->repository->save($entity, true);

        $viewModel = new $this->entityClass();
        $viewModel->fromEntity($entity);

        return $viewModel;
    }

    public function updateEntity(string $id, array $properties): ViewModel
    {
        $viewModel = new $this->entityClass($properties);

        $entity = $this->repository->find($id);
        $entity = $viewModel->toEntity($entity);

        $this->repository->save($entity, true);

        $viewModel = new $this->entityClass();
        $viewModel->fromEntity($entity);

        return $viewModel;
    }

    public function deleteEntity(string $id): void
    {
        $entity = $this->repository->find($id);

        $this->repository->delete($entity, true);
    }
}