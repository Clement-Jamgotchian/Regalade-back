<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\ContainsIngredientRepository;
use App\Repository\RecipeListRepository;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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
    private $paginatorInterface;
    private $request;

    public function __construct(Security $security, RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository, CompareQuantityService $compareQuantityService, RecipeListRepository $recipeListRepository, PaginatorInterface $paginatorInterface, RequestStack $request)
    {
        $this->user = $security->getUser();
        $this->recipeRepository = $recipeRepository;
        $this->containsIngredientRepository = $containsIngredientRepository;
        $this->compareQuantityService = $compareQuantityService;
        $this->recipeListRepository = $recipeListRepository;
        $this->paginatorInterface = $paginatorInterface;
        $this->request = $request->getCurrentRequest();
    }

    public function makeSuggestions()
    {

        $fridge = $this->user->getFridges();

        if ($fridge->isEmpty()) {
            return ['content' => '', 'code' => Response::HTTP_NO_CONTENT];
        }

        $suggestionsArray = [];

        foreach ($this->recipeRepository->findAll() as $recipe) {

            if ($this->recipeListRepository->findOneBy(["recipe" => $recipe, "user" => $this->user])) {
                continue;
            }

            $proposition = ['percent' => '', 'recipe' => $recipe, 'ingredientsOk' => [], 'ingredientsToComplete' => [], 'ingredientsToBuy' => []];

            $count = 0;
            foreach ($recipe->getContainsIngredients() as $containsIngredientElement) {

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
                            $proposition['ingredientsOk'][] = $comparison['ingredient'];
                            $count += 1;
                        } else {
                            $proposition['ingredientsToComplete'][] = $comparison['ingredient'];
                            $count += $comparison['percent'];
                        }
                        
                        break;
                    } 
                }

                if (!$usefulIngredient) {
                    $ingredient = [];
                    $recipePortions = $recipe->getPortions();
                    $portionsWanted = count($this->user->getMembers());
                    $proportion = $portionsWanted / $recipePortions;
                    $ingredient['quantity'] = round($containsIngredientElement->getQuantity() * $proportion);
                    $ingredient['ingredient'] = $containsIngredientElement->getIngredient();
                    
                    $proposition['ingredientsToBuy'][] = $ingredient;
                }

            }

            $proportionOfIngredientsInFridge = $count * 100 / count($recipe->getContainsIngredients());

            $proposition['percent'] = round($proportionOfIngredientsInFridge, 2);
            if ($proportionOfIngredientsInFridge > 0) {
                $suggestionsArray[] = $proposition;
            }
            
        }

        $sortByPercentageOfIngredientInFridge = array_column($suggestionsArray, 'percent');
        array_multisort($sortByPercentageOfIngredientInFridge, SORT_DESC, $suggestionsArray);

        $recipesWithPagination = $this->paginatorInterface->paginate(
            $suggestionsArray,
            $this->request->query->getInt('page', 1),
            12
        );

        $toSend = [];
        $toSend['totalPages'] = ceil($recipesWithPagination->getTotalItemCount() / $recipesWithPagination->getItemNumberPerPage());
        $toSend['recipes'] = $recipesWithPagination;

        return (empty($suggestionsArray)) ? ['content' => '', 'code' => Response::HTTP_NO_CONTENT] : ['content' => $toSend, 'code' => Response::HTTP_CREATED];
        
    }
}