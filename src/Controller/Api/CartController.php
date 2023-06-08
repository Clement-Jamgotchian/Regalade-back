<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/cart", name="app_api_cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(UserService $userService, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

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
}
