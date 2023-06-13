<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Services\AddEditDeleteService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(UserService $userService): JsonResponse
    {
        /** @var User */
        $user = $userService->getCurrentUser();

        $cart = $user->getCarts();

        return $this->json($cart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);
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

        $recipesList = $user->getRecipeLists();

        $allCart = [];
        foreach ($recipesList as $recipesListElement) {
            $ingredients = $recipesListElement->getRecipe()->getContainsIngredients();

            $portionsRecipe = $recipesListElement->getRecipe()->getPortions();
            $portionsWanted = $recipesListElement->getPortions();

            $proportion = $portionsWanted / $portionsRecipe;

            foreach ($ingredients as $ingredient) {
                $newCart = new Cart();
                $newCart->setIngredient($ingredient->getIngredient());
                $newCart->setUser($user);
                $newCart->setQuantity(round($ingredient->getQuantity() * $proportion));

                $allCart[] = $newCart;

                $entityManagerInterface->persist($newCart);
            }
        }

        $entityManagerInterface->flush();

        return $this->json($allCart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);
    }

    /**
     * @Route("", name="delete", methods={"DELETE"})
     */
    public function delete(AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {
        $addEditDeleteService->deleteAll(Cart::class, $cartRepository);

        return $this->json(["message" => "Tous les ingrédients ont été supprimé du panier"], Response::HTTP_OK);
    }

    /**
     * @Route("/add", name="addOne", methods={"POST"})
     * 
     */
    public function addOne(AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {

        $newCart = $addEditDeleteService->add($cartRepository, Cart::class);

        return $this->json($newCart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);
    }

    /**
     * @Route("/{id}", name="deleteOne", requirements={"id"="\d+"}, methods={"DELETE"})
     * 
     */
    public function deleteOne(?Cart $cart, AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {

        $deletedCart = $addEditDeleteService->delete($cart, $cartRepository, Cart::class);

        return $this->json(["message" => $deletedCart[0]], $deletedCart[1]);

    }

    /**
     * @Route("/{id}", name="editOne", requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
     * 
     */
    public function editOne(?Cart $cart, AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {

        $EditedCart = $addEditDeleteService->edit($cart, $cartRepository, Cart::class);

        return $this->json($EditedCart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);

    }
}
