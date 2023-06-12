<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/recipes", name="app_api_recipes_")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, RecipeRepository $recipeRepository, PaginatorInterface $paginatorInterface): JsonResponse
    {
        if(!is_null($request->query->get('search'))) {
            $recipes = $recipeRepository->findWhere($request->query->get('search'));
        } else {
            $recipes = $recipeRepository->findAll();
        }

        $recipesWithPagination = $paginatorInterface->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            12
        );

        return $this->json($recipesWithPagination, 200, [], ['groups' => ["recipe_browse"]]);
    }
    
    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Recipe $recipe): JsonResponse
    {
        if ($recipe === null)
        {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($recipe, 200, [], ['groups' => ["recipe_browse", "recipe_read"]]);
    }

}
