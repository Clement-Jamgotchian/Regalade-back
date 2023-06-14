<?php

namespace App\Controller\Api;

use App\Entity\Fridge;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Repository\ContainsIngredientRepository;
use App\Repository\FridgeRepository;
use App\Repository\RecipeRepository;
use App\Services\AddEditDeleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
     * @Route("/api/fridge", name="app_api_fridge_")
     */

class FridgeController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $fridge = $user->getFridges();

        return $this->json($fridge, 200, [], ['groups' => ["ingredient_read", "fridge_browse"]]);
    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?ingredient $ingredient, FridgeRepository $fridgeRepository):JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $ingredientToRead = $fridgeRepository->findOneByIngredient($ingredient, $user);

        if ($ingredient === null) {
            return $this->json(['message' => "Cet ingrédient n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        if (!$ingredientToRead) {
            return $this->json(['message' => "Cet ingrédient n'est pas dans votre frigo"], Response::HTTP_BAD_REQUEST, []);
        }
        
        return $this->json($ingredientToRead, 200, [], ['groups' => ["ingredient_browse", "ingredient_read", "fridge_ingredient_read"]]);

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
    public function deleteOne(?Ingredient $ingredient, FridgeRepository $fridgeRepository, AddEditDeleteService $addEditDeleteService): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $fridge = $fridgeRepository->findOneByIngredient($ingredient, $user);

        $addEditDeleteService->delete($fridge, $fridgeRepository, Fridge::class);

        return $this->json(["message" => "l'ingrédient a été supprimé du frigo"], Response::HTTP_OK);
    }
    
    /**
    * @Route("/{id}", name="edit",requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
    */
    public function edit(?Ingredient $ingredient, AddEditDeleteService $addEditDeleteService, FridgeRepository $fridgeRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
 
        $fridge = $fridgeRepository->findOneByIngredient($ingredient, $user);
 
        $EditedFridge = $addEditDeleteService->edit($fridge, $fridgeRepository, Fridge::class);
 
        return $this->json($EditedFridge, 200, [], ['groups' => ["ingredient_read", "fridge_browse"]]);
 
    }

    /**
    * @Route("/suggestion", name="generate", methods={"POST"})
    */
    public function generate(RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        // On récupère tout ce qu'il y a dans le frigo
        $inFridge = $user->getFridges();

        $recipes = $recipeRepository->findAll();

        $ko0 = [];
        $moins25 = [];
        $moins50 = [];
        $moins75 = [];
        $plus75 = [];
        $ok100 = [];

        foreach ($recipes as $recipe) {
            $containsIngredient = $recipe->getContainsIngredients();

            $count = 0;
            foreach ($containsIngredient as $containsIngredientElement) {

                foreach ($inFridge as $fridgeElement) {
                    $ingredient = $fridgeElement->getIngredient();

                    if($containsIngredientElement === $containsIngredientRepository->test($ingredient, $recipe)) {
                        $count += 1;
                    }

                }

                $total = $count * 100 / count($inFridge);

            }

            if ($total == 100) {
                $ok100[] = $recipe;
            } else if ($total > 75) {
                $plus75[] = $recipe;
            } else if ($total > 50) {
                $moins75[] = $recipe;
            } else if ($total > 25) {
                $moins50[] = $recipe;
            } else if ($total > 0) {
                $moins25[] = $recipe;
            } else {
                $ko0[] = $recipe;
            }

            
        }

        dd(["100" => $ok100, "+75" => $plus75, "-75" => $moins75, "-50" => $moins50, "-25" => $moins25, "0" => $ko0]);

        return $this->json(["coucou" => "coucou"]);
    }
}