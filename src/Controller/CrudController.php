<?php

namespace Pportelette\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Pportelette\CrudBundle\Repository\CrudRepositoryInterface;
use Pportelette\CrudBundle\Service\CrudServiceInterface;
use Pportelette\CrudBundle\Service\CrudService;

abstract class CrudController extends AbstractController {
    protected $serializer;
    protected $service;
    protected $repository;

    public function configure(
        SerializerInterface $serializer, 
        CrudRepositoryInterface $repository, 
        string $entityClass, 
        CrudServiceInterface $service = null
    ) {
        $this->serializer = $serializer;

        if($service) {
            $this->service = $service;
            $this->service->setRepository($repository);
            $this->service->setEntityClass($entityClass);
        } else {
            $this->service = new CrudService($repository, $entityClass);
        }
    }

    #[Route('/', methods: ['GET'])]
    public function getAll(Request $request): Response
    {
        $params = $request->query->all();
        $page = 1;
        if(isset($params['page'])) {
            $page = $params['page'];
            unset($params['page']);
        }

        $words = $this->service->getAll($page, $params);
        
        return $this->sendResponse($words);
    }

    #[Route('/list', methods: ['GET'])]
    public function getList(Request $request): Response
    {
        $params = $request->query->all();

        $words = $this->service->getList($params);
        
        return $this->sendResponse($words);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getEntity(string $id): Response
    {
        $entity = $this->service->getEntity($id);

        return $this->sendResponse($entity);
    }

    #[Route('/', methods: ['POST'])]
    public function createEntity(Request $request): Response
    {
        $entity = $this->service->createEntity(json_decode($request->getContent(), true));

        return $this->sendResponse($entity, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function updateEntity(Request $request, int $id): Response
    {
        $entity = $this->service->updateEntity($id, json_decode($request->getContent(), true));

        return $this->sendResponse($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteEntity(Request $request, int $id): Response
    {
        $this->service->deleteEntity($id);

        return $this->sendResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function sendResponse($payload, int $statusCode = null): Response
    {
        if(!$statusCode) {
            $statusCode = Response::HTTP_OK;
        }
        $response = new Response(
            $this->serializer->serialize($payload, 'json'),
            $statusCode,
            ['Content-Type' => 'application/json']
        );

        return $response;
    }
}