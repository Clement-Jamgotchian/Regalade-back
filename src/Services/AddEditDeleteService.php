<?php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AddEditDeleteService
{
    private $request;
    private $serializerInterface;
    private $entityManagerInterface;
    private $user;
    private $updateRatingService;

    public function __construct(RequestStack $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface, Security $security, UpdateRatingService
     $updateRatingService)
    {
        $this->request = $request->getCurrentRequest();
        $this->serializerInterface = $serializerInterface;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->user = $security->getUser();
        $this->updateRatingService = $updateRatingService;
    }

    public function add($repository, $entityClass, $newUser = null)
    {

        $newAdd = $this->serializerInterface->deserialize($this->request->getContent(), $entityClass, 'json');

        if ($entityClass === Comment::class) {
            $this->updateRatingService->update($repository, $newAdd->getRecipe(), $newAdd->getRating());
        }

        if($newUser) {
            $newAdd->setUser($newUser);
        } else {
            if ($entityClass !== Ingredient::class) {
                $newAdd->setUser($this->user);
            }
        }

        if(method_exists($newAdd, 'isIsAdult') && $newAdd->isIsAdult() === null) {
            $newAdd->setIsAdult(true);
        }

        $repository->add($newAdd, true);

        return $newAdd;
    }

    public function delete($entity, $repository, $entityClass)
    {

        if ($entity === null) {
            return ["Not found", Response::HTTP_NOT_FOUND];
        }

        $getName ="get".substr($entityClass, 11)."s";

        if (!$this->user->$getName()->contains($entity)) {
            return ["Not for this user", Response::HTTP_BAD_REQUEST];
        }

        $repository->remove($entity, true);

        return ["Delete ok", Response::HTTP_OK];
    }

    public function edit($entity, $repository, $entityClass)
    {

        $this->serializerInterface->deserialize($this->request->getContent(), $entityClass, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $entity]);

        $repository->add($entity, true);

        return $entity;
    }

    public function deleteAll($entityClass, $repository)
    {

        $getName ="get".substr($entityClass, 11)."s";
        $collection = $this->user->$getName();

        foreach ($collection as $element) {
            $repository->remove($element);
        }

        $this->entityManagerInterface->flush();

        return Response::HTTP_OK;
    }

}