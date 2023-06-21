<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\ContainsIngredient;
use App\Entity\Fridge;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\FridgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class CompareQuantityService
{
    private $entityManagerInterface;
    private $fridgeRepository;
    private $cartRepository;
    /** @var User */
    private $user;

    public function __construct(EntityManagerInterface $entityManagerInterface, FridgeRepository $fridgeRepository, Security $security, CartRepository $cartRepository)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->fridgeRepository = $fridgeRepository;
        $this->user = $security->getUser();
        $this->cartRepository = $cartRepository;
    }

    public function compareQuantityToAddInCart()
    {
        $allCart = $this->checkRecipesList(Cart::class, []);

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

        $percent = round(($ingredientInFridge->getQuantity() - $substract) / ($containsIngredientElement2->getQuantity() * $proportion), 2);

        $status = ($quantityToSet < 0) ? true : false;

        $ingredient = ($quantityToSet < 0) ? ['quantity' => round(($containsIngredientElement2->getQuantity() * $proportion)), 'ingredient' => $containsIngredientElement2->getIngredient()] : ['quantity' => round($quantityToSet), 'ingredient' => $containsIngredientElement2->getIngredient() ];

        return ['status' => $status, 'ingredient' => $ingredient, 'percent' => $percent];
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
                    if (!in_array($newCart, $element)) {
                        $element[] = $newCart;
                    }
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

            $cart = $this->cartRepository->findOneByIngredient($containsIngredientElement->getIngredient(), $this->user) ?? new Cart();

            $cart->setIngredient($containsIngredientElement->getIngredient())
                    ->setUser($this->user)
                    ->setQuantity(round($quantityToSet + $cart->getQuantity()));

            $this->cartRepository->add($cart, true);

            return $cart;

        }

    }

    public function calculSubstract($ingredientInFridge, $containsIngredientElement, $proportion, $substract)
    {
        if ($ingredientInFridge) {
            $substract += ($containsIngredientElement->getQuantity() * $proportion);
        }

        return $substract;
    }

    public function addToFridgeAfterCart($cart)
    {
        if(count($cart) == 0)
        {
            return ["Panier vide, impossible de transférer vers le frigo", Response::HTTP_BAD_REQUEST];
        }

        foreach ($cart as $cartElement) {
            $ingredient = $cartElement->getIngredient();
            $quantity = $cartElement->getQuantity();

            $fridgeElement = $this->fridgeRepository->findOneByIngredient($ingredient, $this->user) ?? new Fridge();
    
            $fridgeElement->setIngredient($ingredient)
                          ->setUser($this->user)
                          ->setQuantity(round($quantity + $fridgeElement->getQuantity()));

            $this->fridgeRepository->add($fridgeElement, true);
            $this->cartRepository->remove($cartElement, true);
        }

        return ["Tous les éléments du panier ont été transféré vers le frigo", Response::HTTP_ACCEPTED];

    }

    public function cleanFridge(RecipeList $recipeList)
    {

        $fridge = $this->user->getFridges();
        foreach ($fridge as $fridgeElement) {
            foreach ($recipeList->getRecipe()->getContainsIngredients() as $contains)
            {
                if ($fridgeElement->getIngredient() === $contains->getIngredient())
                {
                    $recipePortions = $recipeList->getRecipe()->getPortions();
                    $portionsWanted = $recipeList->getPortions();
                    $proportion = $portionsWanted / $recipePortions;

                    $quantity = ($contains->getQuantity() * $proportion);

                    if (($fridgeElement->getQuantity() - $quantity) <= 0) {
                        $this->fridgeRepository->remove($fridgeElement, true);
                    } else {
                        $fridgeElement->setQuantity($fridgeElement->getQuantity() - $quantity);
                        $this->fridgeRepository->add($fridgeElement, true);
                    }
                }
            }
        }

    }

}