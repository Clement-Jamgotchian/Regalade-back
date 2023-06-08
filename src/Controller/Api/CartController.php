<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\IngredientRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/api/cart", name="app_api_cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(UserService $userService): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        $cart = $user->getCarts();

        return $this->json($cart, 200, [], ['groups' => ["ingredient_browse", "cart_browse"]]);
    }

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

    /**
     * @Route("/add", name="addOne", methods={"POST"})
     * 
     */
    public function addOne(Request $request, SerializerInterface $serializerInterface, UserService $userService, CartRepository $cartRepository): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        $newCart = $serializerInterface->deserialize($request->getContent(), Cart::class, 'json');

        // dd($newCart);

        $newCart->setUser($user);

        $cartRepository->add($newCart, true);

        return $this->json($newCart, 200, [], ['groups' => ["ingredient_browse", "cart_browse"]]);
    }
}
