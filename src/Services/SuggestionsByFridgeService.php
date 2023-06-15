<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\ContainsIngredientRepository;
use App\Repository\RecipeListRepository;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class SuggestionsByFridgeService
{
    /** @var User */
    private $user;
    private $recipeRepository;
    private $containsIngredientRepository;
    private $compareQuantityService;
    private $recipeListRepository;

    public function __construct(Security $security, RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository, CompareQuantityService $compareQuantityService, RecipeListRepository $recipeListRepository)
    {
        $this->user = $security->getUser();
        $this->recipeRepository = $recipeRepository;
        $this->containsIngredientRepository = $containsIngredientRepository;
        $this->compareQuantityService = $compareQuantityService;
        $this->recipeListRepository = $recipeListRepository;
    }

    public function makeSuggestions()
    {

        $fridge = $this->user->getFridges();

        if ($fridge->isEmpty()) {
            return ['content' => '', 'code' => Response::HTTP_NO_CONTENT];
        }

        $suggestionsArray = ['100%' => [], '76-99%' => [], '51-75%' => [], '26-50%' => [], '1-25%' => []];

        $recipes = $this->recipeRepository->findAll();
        foreach ($recipes as $recipe) {

            if ($this->recipeListRepository->findOneByRecipe($recipe, $this->user)) {
                continue;
            }

            $proposition = ['recipe' => $recipe, 'ingredientsOk' => [], 'ingredientsToComplete' => [], 'ingredientsToBuy' => []];

            $containsIngredient = $recipe->getContainsIngredients();
            $count = 0;
            foreach ($containsIngredient as $containsIngredientElement) {

                foreach ($fridge as $fridgeElement) {
                    $ingredient = $fridgeElement->getIngredient();

                    $usefulIngredient = $containsIngredientElement === $this->containsIngredientRepository->ingredientIsInRecipe($ingredient, $recipe);

                    if($usefulIngredient) {
                        
                        $comparison = $this->compareQuantityService->compareQuantityToMakeSuggestion($recipe, $containsIngredientElement);

                        if (is_null($comparison)) {
                            $ingredient = [];
                            $ingredient['quantity'] = $containsIngredientElement->getQuantity();
                            $ingredient['ingredient'] = $containsIngredientElement->getIngredient();
                            
                            $proposition['ingredientsToBuy'][] = $ingredient;
                        } else if ($comparison['status']) {
                            $proposition['ingredientsOk'][] = $comparison;
                            $count += 1;
                        } else {
                            $proposition['ingredientsToComplete'][] = $comparison;
                            $count += 0.5;
                        }
                        
                        break;
                    } 
                }

                if (!$usefulIngredient) {
                    $ingredient = [];
                    $ingredient['quantity'] = $containsIngredientElement->getQuantity();
                    $ingredient['ingredient'] = $containsIngredientElement->getIngredient();
                    
                    $proposition['ingredientsToBuy'][] = $ingredient;
                }

            }

            $proportionOfIngredientsInFridge = $count * 100 / count($containsIngredient);

            if ($proportionOfIngredientsInFridge == 100) {
                $suggestionsArray['100%'][] = $proposition;
            } else if ($proportionOfIngredientsInFridge > 75) {
                $suggestionsArray['76-99%'][] = $proposition;
            } else if ($proportionOfIngredientsInFridge > 50) {
                $suggestionsArray['51-75%'][] = $proposition;
            } else if ($proportionOfIngredientsInFridge > 25) {
                $suggestionsArray['26-50%'][] = $proposition;
            } else if ($proportionOfIngredientsInFridge > 0) {
                $suggestionsArray['1-25%'][] = $proposition;
            }
            
        }

        return (empty($suggestionsArray)) ? ['content' => '', 'code' => Response::HTTP_NO_CONTENT] : ['content' => $suggestionsArray, 'code' => Response::HTTP_CREATED];
        
    }
}