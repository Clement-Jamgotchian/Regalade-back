<?php

namespace App\Controller\Api;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Services\AddEditDeleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/ingredients", name="app_api_ingredients_")
 */
class IngredientController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        $ingredients = (!is_null($request->query->get('search'))) ? $ingredientRepository->findWhere($request->query->get('search'))
                                                                  : $ingredientRepository->findAll();
        
        if (empty($ingredients)) {
            return $this->json('', Response::HTTP_NO_CONTENT, []);
        }

        return $this->json($ingredients, 200, [], ['groups' => ["ingredient_browse"]]);
    }
    
    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Ingredient $ingredient): JsonResponse
    {
        if ($ingredient === null) {
            return $this->json(['message' => "Cet ingrédient n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($ingredient, 200, [], ['groups' => ["ingredient_browse", "ingredient_read"]]);
    }

    /**
    * @Route("", name="add", methods={"POST"})
    */

    public function add(AddEditDeleteService $addEditDeleteService, IngredientRepository $ingredientRepository): JsonResponse
    {
         $newIngredient = $addEditDeleteService->add($ingredientRepository, Ingredient::class);
 
         return $this->json($newIngredient, Response::HTTP_CREATED, [], ['groups' => ["ingredient_browse", "ingredient_read"]]);
    }

    /**
    * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
    */
    public function edit(?Ingredient $ingredient, AddEditDeleteService $addEditDeleteService, IngredientRepository $ingredientRepository): JsonResponse
    {
         $editedIngredient = $addEditDeleteService->edit($ingredient, $ingredientRepository, Ingredient::class);
 
         return $this->json($editedIngredient, Response::HTTP_OK, [], ['groups' => ["ingredient_browse", "ingredient_read"]]);
    }

}
