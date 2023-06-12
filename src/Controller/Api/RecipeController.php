<?php

namespace App\Controller\Api;

use App\Entity\ContainsIngredient;
use App\Entity\Recipe;
use App\Repository\ContainsIngredientRepository;
use App\Repository\RecipeRepository;
use App\Services\AddEditDeleteService;
use App\Services\UserService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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

        $toSend = [];
        $toSend['totalPages'] = ceil($recipesWithPagination->getTotalItemCount() / $recipesWithPagination->getItemNumberPerPage());
        $toSend['recipes'] = $recipesWithPagination;

        return $this->json($toSend, 200, [], ['groups' => ["recipe_browse"]]);
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

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(AddEditDeleteService $addEditDeleteService, RecipeRepository $recipeRepository): JsonResponse
    {
        $recipe = $addEditDeleteService->add($recipeRepository, Recipe::class);
        
        return $this->json($recipe, 200, [], ['groups' => ["recipe_browse", "recipe_read"]]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(?Recipe $recipe, AddEditDeleteService $addEditDeleteService, RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository): JsonResponse
    {
        $ingredientsInRecipe = $containsIngredientRepository->findByRecipe($recipe);

        foreach ($ingredientsInRecipe as $ingredient) {
            $containsIngredientRepository->remove($ingredient, true);
        }
        
        $deletedRecipe = $addEditDeleteService->delete($recipe, $recipeRepository, Recipe::class);

        return $this->json(["message" => $deletedRecipe[0]], $deletedRecipe[1]);
    }


}
