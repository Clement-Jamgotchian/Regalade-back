<?php

namespace App\Controller\Api;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function browse(ingredientRepository $ingredientRepository): JsonResponse
    {
        $ingredients = $ingredientRepository->findAll();

        return $this->json($ingredients, 200, [], ['groups' => ["ingredient_browse"]]);
    }
    
    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(Ingredient $ingredient): JsonResponse
    {
        if ($ingredient === null) {
            return $this->json(['message' => "Cet ingrédient n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($ingredient, 200, [], ['groups' => ["ingredient_browse", "ingredient_read"]]);
    }
}
