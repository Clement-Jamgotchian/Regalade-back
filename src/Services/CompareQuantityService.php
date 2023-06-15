<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\ContainsIngredient;
use App\Entity\Fridge;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Repository\FridgeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class CompareQuantityService
{
    private $entityManagerInterface;
    private $fridgeRepository;

    public function __construct(EntityManagerInterface $entityManagerInterface, FridgeRepository $fridgeRepository)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->fridgeRepository = $fridgeRepository;
    }

    public function compare(Collection $recipesList, User $user)
    {
        $allCart = [];
        foreach ($recipesList as $recipesListElement) {
            $containsIngredient = $recipesListElement->getRecipe()->getContainsIngredients();

            $recipePortions = $recipesListElement->getRecipe()->getPortions();
            $portionsWanted = $recipesListElement->getPortions();

            $proportion = $portionsWanted / $recipePortions;

            foreach ($containsIngredient as $containsIngredientElement) {

                $ingredientInFridge = $this->fridgeRepository->findOneByIngredient($containsIngredientElement->getIngredient(), $user);

                if (!is_null($ingredientInFridge)) {
                    $quantityToSet = ($containsIngredientElement->getQuantity() * $proportion) - $ingredientInFridge->getQuantity();
                } else {
                    $quantityToSet = $containsIngredientElement->getQuantity() * $proportion;
                }

                if ($quantityToSet > 0) {
                    $newCart = new Cart();
                    $newCart->setIngredient($containsIngredientElement->getIngredient());
                    $newCart->setUser($user);
                    $newCart->setQuantity(round($quantityToSet));

                    $allCart[] = $newCart;

                    $this->entityManagerInterface->persist($newCart);
                }
                
            }
        }

        $this->entityManagerInterface->flush();
        return $allCart;
    }

    public function compareFridge(Recipe $recipe, User $user, ContainsIngredient $containsIngredientElement)
    {
            $recipePortions = $recipe->getPortions();
            $portionsWanted = count($user->getMembers());

            $proportion = $portionsWanted / $recipePortions;

                $ingredientInFridge = $this->fridgeRepository->findOneByIngredient($containsIngredientElement->getIngredient(), $user);

                // dd($ingredientInFridge);

                    $quantityToSet = ($containsIngredientElement->getQuantity() * $proportion) - $ingredientInFridge->getQuantity();

                    $ingredient = [];
                if ($quantityToSet > 0) {

                    $ingredient['quantity'] = round($quantityToSet);
                    $ingredient['ingredient'] = $containsIngredientElement->getIngredient();

                    // dd($ingredient);
                
            }

        return $ingredient;
    }
}