<?php

namespace App\Controller\Api;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/recipes", name="app_api_recipes_")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("", name="browse")
     */
    public function browse(RecipeRepository $recipeRepository): JsonResponse
    {

        $recipes = $recipeRepository->findAll();

        return $this->json($recipes, 200, [], ['groups' => ["recipe_browse"]]);
    }
}
