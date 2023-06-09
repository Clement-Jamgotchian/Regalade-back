<?php

namespace App\Controller\Api;

use App\Entity\Fridge;
use App\Entity\User;
use App\Repository\FridgeRepository;
use App\Repository\IngredientRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use App\Services\AddEditDeleteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
     * @Route("/api/fridge", name="app_api_fridge_")
     */

class FridgeController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(UserService $userService): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        $fridge = $user->getFridges();

        return $this->json($fridge, 200, [], ['groups' => ["ingredient_read", "fridge_browse"]]);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(FridgeRepository $fridgeRepository, AddEditDeleteService $addEditDeleteService): JsonResponse
    {
        $fridge = $addEditDeleteService->add($fridgeRepository, Fridge::class);


        return $this->json($fridge, Response::HTTP_CREATED, [], ['groups' => ["ingredient_read", "fridge_browse"]]);
    }

    /**
     * @Route("", name="delete", methods={"DELETE"})
     */
    public function delete(FridgeRepository $fridgeRepository, AddEditDeleteService $addEditDeleteService): JsonResponse
    {
        $addEditDeleteService->deleteAll(Fridge::class, $fridgeRepository);

        return $this->json(["message" => "Tous les ingrédients ont été supprimés du frigo"], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="deleteOne",requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function deleteOne(?Fridge $fridge, FridgeRepository $fridgeRepository, AddEditDeleteService $addEditDeleteService): JsonResponse
    {
        $fridge = $addEditDeleteService->delete($fridge, $fridgeRepository, Fridge::class);

        return $this->json(["message" => "l'ingrédient a été supprimé du frigo"], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="deleteOne",requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
     */
    public function edit(?Fridge $fridge, FridgeRepository $fridgeRepository, AddEditDeleteService $addEditDeleteService): JsonResponse
    {
        $fridge = $addEditDeleteService->edit($fridge, $fridgeRepository, Fridge::class);

        return $this->json($fridge, 200, [], ['groups' => ["ingredient_read", "fridge_browse"]]);

    }
}