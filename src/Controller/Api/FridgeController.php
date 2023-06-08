<?php

namespace App\Controller\Api;

use App\Entity\Fridge;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Repository\FridgeRepository;
use App\Repository\IngredientRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
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

        return $this->json($fridge, 200, [], ['groups' => ["ingredient_browse", "fridge_browse"]]);
    }
    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, SerializerInterface $serializerInterface ,UserRepository $userRepository,FridgeRepository $fridgeRepository, IngredientRepository $ingredientRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        $fridge = $serializerInterface->deserialize($request->getContent(), Fridge::class,"json");
        
        $fridge->setUser($user);

        $fridgeRepository->add($fridge, true);

        // $ingredientId = json_decode($request->getContent(), true)["id"];
        // $ingredientQuantity = json_decode($request->getContent(), true)["quantity"];
        // $ingredient = $ingredientRepository->find($ingredientId);
        // $fridge = new Fridge();
        // $fridge->setIngredient($ingredient);

        // if ($ingredient === null) {
        //     return $this->json(['message' => "Cet ingredient n'existe pas"], Response::HTTP_NOT_FOUND, []);
        // }
        // $user->addFridge($ingredientId);

        // $userRepository->add($user, true);


        return $this->json(["message" => "Ingrédient ajoutée au frigo"], Response::HTTP_CREATED);

    }

    /**
     * @Route("", name="delete", methods={"DELETE"})
     */
    public function delete(UserService $userService, FridgeRepository $fridgeRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        $fridge = $user->getFridges();

        foreach ($fridge as $fridgeElement) {
            $fridgeRepository->remove($fridgeElement);
        }

        $entityManagerInterface->flush();

        return $this->json(["message" => "Tous les ingrédients ont été supprimé du panier"], Response::HTTP_OK);
    }
}
