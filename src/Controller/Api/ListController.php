<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 *
 * @Route("/api/list", name="app_api_list_")
 */
class ListController extends AbstractController
{
        /**
     *  afficher la liste des repas
     *
     * @Route("", name="browse", methods = {"GET"})
     */
    public function browse(Request $request, UserRepository $userRepository, RecipeRepository $recipeRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();
        
        $user->getRecipe();

        $userRepository->remove($user, true);

        return $this->json(["message" => "Recette supprimée de la liste de repas"], Response::HTTP_CREATED);

    }
    /**
     * ajouter un repas à la liste
     *
     * @Route("", name="add", methods = {"POST"})
     */
    public function add(Request $request, UserRepository $userRepository, RecipeRepository $recipeRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        $recipeId = json_decode($request->getContent(), true)["id"];
        $recipe = $recipeRepository->find($recipeId);

        $user->addRecipe($recipe);

        $userRepository->add($user, true);

        return $this->json(["message" => "Recette ajoutée à la liste de repas"], Response::HTTP_CREATED);

    }
    /**
     * suprimer un repas à la liste
     *
     * @Route("/{id}_delete", name="delete", methods = {"DELETE"})
     */
    public function remove(Request $request, UserRepository $userRepository, RecipeRepository $recipeRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        $recipeId = json_decode($request->getContent(), true)["id"];
        $recipe = $recipeRepository->find($recipeId);

        $user->removeRecipe($recipe);

        $userRepository->remove($user, true);

        return $this->json(["message" => "Recette supprimée de la liste de repas"], Response::HTTP_CREATED);

    }
}
