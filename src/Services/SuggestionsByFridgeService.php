<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\ContainsIngredientRepository;
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

    public function __construct(Security $security, RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository, CompareQuantityService $compareQuantityService)
    {
        $this->user = $security->getUser();
        $this->recipeRepository = $recipeRepository;
        $this->containsIngredientRepository = $containsIngredientRepository;
        $this->compareQuantityService = $compareQuantityService;
    }

    public function suggest()
    {

        $inFridge = $this->user->getFridges();

        $return = [];

        if ($inFridge->isEmpty()) {
            $return['content'] = '';
            $return['code'] = Response::HTTP_NO_CONTENT;
            return $return;
        }

        $recipes = $this->recipeRepository->findAll();

        $propsrecipes = [];
        
        $ko0 = [];

        foreach ($recipes as $recipe) {
            $toSend = [];
            $toSend['recipe'] = $recipe;
            $toSend['ingredientsOk'] = [];
            $toSend['ingredientsToComplete'] = [];
            $toSend['ingredientsToBuy'] = [];
            $containsIngredient = $recipe->getContainsIngredients();

            $count = 0;
            foreach ($containsIngredient as $containsIngredientElement) {

                foreach ($inFridge as $fridgeElement) {
                    $ingredient = $fridgeElement->getIngredient();

                    $usefulIngredient = $containsIngredientElement === $this->containsIngredientRepository->test($ingredient, $recipe);

                    if($usefulIngredient) {
                        $count += 1;
                        
                        $comparison = $this->compareQuantityService->compareFridge($recipe, $this->user, $containsIngredientElement);

                        if ($comparison['quantity'] < 0) {
                            $ingredient = [];
                            $ingredient['quantity'] = $fridgeElement->getQuantity();
                            $ingredient['ingredient'] = $containsIngredientElement->getIngredient();
                            $toSend['ingredientsOk'][] = $ingredient;
                        } else {
                            $toSend['ingredientsToComplete'][] = $this->compareQuantityService->compareFridge($recipe, $this->user, $containsIngredientElement);
                        }
                        
                        break;
                    } 
                }

                if (!$usefulIngredient) {
                    $ingredient = [];
                    $ingredient['quantity'] = $containsIngredientElement->getQuantity();
                    $ingredient['ingredient'] = $containsIngredientElement->getIngredient();
                    
                    $toSend['ingredientsToBuy'][] = $ingredient;
                }

                
            }

            $total = $count * 100 / count($containsIngredient);

            if ($total == 100) {
                $propsrecipes['100%'][] = $toSend;
            } else if ($total > 75) {
                $propsrecipes['75-99%'][] = $toSend;
            } else if ($total > 50) {
                $propsrecipes['51-75%'][] = $toSend;
            } else if ($total > 25) {
                $propsrecipes['26-50%'][] = $toSend;
            } else if ($total > 0) {
                $propsrecipes['1-25%'][] = $toSend;
            } else {
                $ko0[] = $toSend;
            }
            
        }

        if (empty($propsrecipes)) {
            $return['content'] = '';
            $return['code'] = Response::HTTP_NO_CONTENT;
        } else {
            $return['content'] = $propsrecipes;
            $return['code'] = Response::HTTP_CREATED;
        }

        return $return;

    }
}