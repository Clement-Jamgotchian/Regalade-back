<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\ContainsIngredient;
use App\Entity\Fridge;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\FridgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CompareQuantityService
{
    private $entityManagerInterface;
    private $fridgeRepository;
    /** @var User */
    private $user;

    public function __construct(EntityManagerInterface $entityManagerInterface, FridgeRepository $fridgeRepository, Security $security)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->fridgeRepository = $fridgeRepository;
        $this->user = $security->getUser();
    }

    public function compareQuantityToAddInCart()
    {
        $allCart = $this->checkRecipesList(Cart::class, []);

        $this->entityManagerInterface->flush();

        return $allCart;
    }

    public function compareQuantityToMakeSuggestion(Recipe $recipe, ContainsIngredient $containsIngredientElement2)
    {

        $substract = $this->checkRecipesList(Fridge::class, 0);

        $recipePortions = $recipe->getPortions();
        $portionsWanted = count($this->user->getMembers());
        $proportion = $portionsWanted / $recipePortions;

        $ingredientInFridge = $this->fridgeRepository->findOneByIngredient($containsIngredientElement2->getIngredient(), $this->user);

        if (is_null($ingredientInFridge)) {
            return null;
        }

        $quantityToSet = ($containsIngredientElement2->getQuantity() * $proportion) - ($ingredientInFridge->getQuantity() - $substract);

        $ingredient = ($quantityToSet < 0) ? ['status' => true, 'quantity' => round(($containsIngredientElement2->getQuantity() * $proportion)), 'ingredient' => $containsIngredientElement2->getIngredient()] : ['status' => false, 'quantity' => round($quantityToSet), 'ingredient' => $containsIngredientElement2->getIngredient() ];

        return $ingredient;
    }

    public function checkRecipesList($entityClass, $element = null)
    {
        $recipesList = $this->user->getRecipeLists();
        foreach ($recipesList as $recipesListElement) {
            $containsIngredient = $recipesListElement->getRecipe()->getContainsIngredients();

            $recipePortions = $recipesListElement->getRecipe()->getPortions();
            $portionsWanted = $recipesListElement->getPortions();
            $proportion = $portionsWanted / $recipePortions;

            foreach ($containsIngredient as $containsIngredientElement) {

                $ingredientInFridge = $this->fridgeRepository->findOneByIngredient($containsIngredientElement->getIngredient(), $this->user);

                if ($entityClass === Cart::class) {
                    $newCart = $this->addToCart($ingredientInFridge, $containsIngredientElement, $proportion);
                    $element[] = $newCart;
                } 
                
                if ($entityClass === Fridge::class) {
                    return $this->calculSubstract($ingredientInFridge, $containsIngredientElement, $proportion, $element);
                }

            }
        }

        if ($entityClass === Cart::class) {
            return $element;
        }
    }

    public function addToCart($ingredientInFridge, $containsIngredientElement, $proportion)
    {

        $quantityToSet = (!is_null($ingredientInFridge)) ? ($containsIngredientElement->getQuantity() * $proportion) - $ingredientInFridge->getQuantity() : $containsIngredientElement->getQuantity() * $proportion;

        if ($quantityToSet > 0) {
            $newCart = new Cart();
            $newCart->setIngredient($containsIngredientElement->getIngredient())
                    ->setUser($this->user)
                    ->setQuantity(round($quantityToSet));

            $this->entityManagerInterface->persist($newCart);

            return $newCart;
        }

    }

    public function calculSubstract($ingredientInFridge, $containsIngredientElement, $proportion, $substract)
    {
        if ($ingredientInFridge) {
            $substract += ($containsIngredientElement->getQuantity() * $proportion);
        }

        return $substract;
    }

}