<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Repository\AllergenRepository;
use App\Repository\ContainsIngredientRepository;
use App\Repository\DietRepository;
use App\Repository\RecipeRepository;
use App\Services\AddEditDeleteService;
use App\Services\AllergenDietService;
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
    public function browse(Request $request, RecipeRepository $recipeRepository, PaginatorInterface $paginatorInterface, AllergenDietService $allergenDietService): JsonResponse
    {
        
        if(!is_null($request->query->get('search'))) {
            $recipes = $recipeRepository->findWhere($request->query->get('search'));
        } else {
            $recipes = $recipeRepository->findMotherRecipes();
        }

        $allergenRecipes = $allergenDietService->hideRecipesWithAllergen();
        if (!empty($allergenRecipes)) {
            foreach ($recipes as $key => $recipe) {
                if (in_array($recipe, $allergenRecipes)) {
                    unset($recipes[$key]);
                }
            }
        }

        $noDietRecipes = $allergenDietService->hideRecipesWithoutDiet();
        if (!empty($noDietRecipes)) {
            foreach ($recipes as $key => $recipe) {
                if (in_array($recipe, $noDietRecipes)) {
                    unset($recipes[$key]);
                }
            }
        }

        $recipesWithPagination = $paginatorInterface->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            12
        );

        $toSend = [];
        $toSend['totalPages'] = ceil($recipesWithPagination->getTotalItemCount() / $recipesWithPagination->getItemNumberPerPage());
        $toSend['recipes'] = $recipesWithPagination;

        if(empty($recipes)) {
            return $this->json('', Response::HTTP_NO_CONTENT, []);
        }

        return $this->json($toSend, 200, [], ['groups' => ["recipe_browse"]]);
    }

    /**
     * @Route("/home", name="browseForHome", methods={"GET"})
     */
    public function browseForHome(Request $request, RecipeRepository $recipeRepository): JsonResponse
    {
        if ($request->query->get('category') === "new") {
            $recipes = $recipeRepository->findNew();
        } else {
            $recipes = $recipeRepository->findTop($request->query->get('category'));
        }

        if(empty($recipes)) {
            return $this->json('', Response::HTTP_NO_CONTENT, []);
        }

        return $this->json($recipes, 200, [], ['groups' => ["recipe_browse"]]);
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
        
        return $this->json($recipe, 200, [], ['groups' => ["recipe_browse", "recipe_read", "recipe_duplicate"]]);
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
     * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
     */
    public function edit(?Recipe $recipe, AddEditDeleteService $addEditDeleteService, RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if (!$user->getRecipes()->contains($recipe)) {

            $editedRecipe = $addEditDeleteService->add($recipeRepository, Recipe::class);
            $editedRecipe->setMotherRecipe($recipe);
            $editedRecipe->setIsValidate(null);
            $recipeRepository->add($editedRecipe, true);

        } else {
            $ingredients = $containsIngredientRepository->findByRecipe($recipe);
            foreach ($ingredients as $ingredient) {
                $containsIngredientRepository->remove($ingredient, true);
            }
            
            $editedRecipe = $addEditDeleteService->edit($recipe, $recipeRepository, Recipe::class);
        }
        
        return $this->json($editedRecipe, 200, [], ['groups' => ["recipe_browse", "recipe_read"]]);
    }

    /**
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
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

    /**
     * @Route("/my", name="browseMy", methods={"GET"})
     */
    public function browseMy(Request $request, PaginatorInterface $paginatorInterface): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $recipesCollection = $user->getRecipes();
        $recipes = $recipesCollection->getValues();

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


}
