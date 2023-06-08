<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/cart", name="app_api_cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(UserService $userService, CartRepository $cartRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        // Before add, purge old list
        $cart = $user->getCarts();
        foreach ($cart as $cartElement) {
            $cartRepository->remove($cartElement);
        }

        $recipesList = $user->getRecipe();

        $allCart = [];
        foreach ($recipesList as $recipe) {
            $ingredients = $recipe->getContainsIngredients();

            foreach ($ingredients as $ingredient) {
                $newCart = new Cart();
                $newCart->setIngredient($ingredient->getIngredient());
                $newCart->setUser($user);
                $newCart->setQuantity($ingredient->getQuantity());

                $allCart[] = $newCart;

                $entityManagerInterface->persist($newCart);
            }
        }

        $entityManagerInterface->flush();

        return $this->json($allCart, 200, [], ['groups' => ["ingredient_browse", "cart_browse"]]);
    }

    /**
     * @Route("", name="delete", methods={"DELETE"})
     */
    public function delete(UserService $userService, CartRepository $cartRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        $cart = $user->getCarts();

        foreach ($cart as $cartElement) {
            $cartRepository->remove($cartElement);
        }

        $entityManagerInterface->flush();

        return $this->json(["message" => "Tous les ingrédients ont été supprimé du panier"], Response::HTTP_OK);
    }
}
