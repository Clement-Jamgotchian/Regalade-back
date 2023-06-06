<?php

namespace App\Controller\Api;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function read($id, IngredientRepository $ingredientRepository): JsonResponse
    {
        
        return $this->json($ingredientRepository->find($id), 200, [], ['groups' => ["ingredient_browse", "ingredient_read"]]);
    }
}
