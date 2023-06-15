<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\ContainsIngredient;
use App\Entity\Fridge;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Repository\FridgeRepository;
use App\Repository\RecipeListRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CompareQuantityService
{
    private $entityManagerInterface;
    private $fridgeRepository;
    private $recipeListRepository;
    /** @var User */
    private $user;

    public function __construct(EntityManagerInterface $entityManagerInterface, FridgeRepository $fridgeRepository, RecipeListRepository $recipeListRepository, Security $security)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->fridgeRepository = $fridgeRepository;
        $this->recipeListRepository = $recipeListRepository;
        $this->user = $security->getUser();
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

    public function compareFridge(Recipe $recipe, User $user, ContainsIngredient $containsIngredientElement2)
    {
        $recipesList = $this->user->getRecipeLists();

        $substract = 0;

        foreach ($recipesList as $recipesListElement) {
            $containsIngredient = $recipesListElement->getRecipe()->getContainsIngredients();

            $recipePortions = $recipesListElement->getRecipe()->getPortions();
            $portionsWanted = $recipesListElement->getPortions();

            $proportion = $portionsWanted / $recipePortions;

            foreach ($containsIngredient as $containsIngredientElement) {

                $ingredientInFridge = $this->fridgeRepository->findOneByIngredient($containsIngredientElement->getIngredient(), $user);

                if ($ingredientInFridge) {
                    $quantityToSubstract = ($containsIngredientElement->getQuantity() * $proportion);
                    $substract += $quantityToSubstract;
                } 
            }
        }

        $recipePortions = $recipe->getPortions();
        $portionsWanted = count($user->getMembers());

        $proportion = $portionsWanted / $recipePortions;

        $ingredientInFridge = $this->fridgeRepository->findOneByIngredient($containsIngredientElement2->getIngredient(), $user);

        if (is_null($ingredientInFridge)) {
            return null;
        }

        $quantityToSet = ($containsIngredientElement2->getQuantity() * $proportion) - ( $ingredientInFridge->getQuantity() - $substract );

        $ingredient = [];

        if ($quantityToSet < 0) {
            $ingredient['quantity'] = round(($containsIngredientElement2->getQuantity() * $proportion));
            $ingredient['status'] = true;

        } else {
            $ingredient['quantity'] = round($quantityToSet);
            $ingredient['status'] =  false;
        }

        
        $ingredient['ingredient'] = $containsIngredientElement2->getIngredient();


        return $ingredient;
    }
}