<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Services\AddEditDeleteService;
use App\Services\CompareQuantityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function browse(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $cart = $user->getCarts();

        return $this->json($cart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(CartRepository $cartRepository, CompareQuantityService $compareQuantityService): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        // Before add, purge old list
        $cart = $user->getCarts();
        foreach ($cart as $cartElement) {
            $cartRepository->remove($cartElement);
        }

        $allCart = $compareQuantityService->compareQuantityToAddInCart();

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
    public function addOne(Request $request, AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {
        $cartExist = $cartRepository->findOneBy(["ingredient" => $request->toArray()["ingredient"], "user" => $this->getUser()]);
        $quantity = $request->toArray()["quantity"];

        if ($cartExist) {
            $cart = $cartExist;
            $cart->setQuantity(round($quantity + $cart->getQuantity()));
            $cartRepository->add($cart, true);
        } else {
            $cart = $addEditDeleteService->add($cartRepository, Cart::class);
        }

        return $this->json($cart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);
    }

    /**
     * @Route("/{id}", name="deleteOne", requirements={"id"="\d+"}, methods={"DELETE"})
     * 
     */
    public function deleteOne(?Ingredient $ingredient, AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $cart = $cartRepository->findOneBy(["ingredient" => $ingredient, "user" => $user]);

        $deletedCart = $addEditDeleteService->delete($cart, $cartRepository, Cart::class);

        return $this->json(["message" => $deletedCart[0]], $deletedCart[1]);

    }

    /**
     * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
     * 
     */
    public function edit(?Ingredient $ingredient, AddEditDeleteService $addEditDeleteService, CartRepository $cartRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $cart = $cartRepository->findOneBy(["ingredient" => $ingredient, "user" => $user]);

        $EditedCart = $addEditDeleteService->edit($cart, $cartRepository, Cart::class);

        return $this->json($EditedCart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);

    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     * 
     */
    public function read(?Ingredient $ingredient, CartRepository $cartRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $cart = $cartRepository->findOneBy(["ingredient" => $ingredient, "user" => $user]);

        if ($ingredient === null)
        {
            return $this->json(['message' => "Cet ingrédient n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if (!$cart) {
            return $this->json(['message' => "Cet ingrédient n'est pas dans votre panier"], Response::HTTP_BAD_REQUEST, []);
        }

        return $this->json($cart, 200, [], ['groups' => ["ingredient_read", "cart_browse"]]);

    }

    /**
     * @Route("/to-fridge", name="transfer", methods={"POST"})
     */
    public function transfer(CompareQuantityService $compareQuantityService): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $transfer = $compareQuantityService->addToFridgeAfterCart($user->getCarts());

        return $this->json(['message' => $transfer[0]], $transfer[1], []);
    }
}
