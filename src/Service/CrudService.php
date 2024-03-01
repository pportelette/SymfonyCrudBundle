<?php

namespace Pportelette\CrudBundle\Service;

use Pportelette\CrudBundle\Repository\CrudRepositoryInterface;
use Pportelette\PageableBundle\Model\Pageable;
use Pportelette\CrudBundle\Model\ViewModel;

class CrudService implements CrudServiceInterface
{
    protected $repository;
    protected $entityClass;
    
    public function __construct(CrudRepositoryInterface $repository, string $entityClass = null)
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
        $wordsPage = $this->repository->getAll($page, $filters);

        $wordsPage->results = array_map(function ($word) {
            $viewModel = new $this->entityClass();
            $viewModel->fromEntity($word);
            return $viewModel->getAll();
        }, $wordsPage->results);
        
        return $wordsPage;
    }

    public function getList(array $filters = []): array
    {
        $words = $this->repository->getList($filters);

        $words = array_map(function ($word) {
            $viewModel = new $this->entityClass();
            $viewModel->fromEntity($word);
            return $viewModel->getList();
        }, $words);
        
        return $words;
    }

    public function getEntity(int $id): ViewModel
    {
        $entity = $this->repository->find($id);
        $viewModel = new $this->entityClass();
        $viewModel->fromEntity($entity);

        return $viewModel;
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

    public function updateEntity(int $id, array $properties): ViewModel
    {
        $viewModel = new $this->entityClass($properties);

        $entity = $this->repository->find($id);
        $entity = $viewModel->toEntity($entity);

        $this->repository->save($entity, true);

        $viewModel = new $this->entityClass();
        $viewModel->fromEntity($entity);

        return $viewModel;
    }

    public function deleteEntity(int $id): void
    {
        $entity = $this->repository->find($id);

        $this->repository->delete($entity, true);
    }
}