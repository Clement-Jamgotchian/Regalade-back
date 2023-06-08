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

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        $user->addRecipe($recipe);

        $userRepository->add($user, true);

        return $this->json(["message" => "Recette ajoutée à la liste de repas"], Response::HTTP_CREATED);

    }

    /**
     * supprimer un repas de la liste
     *
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function remove(?Recipe $recipe, UserRepository $userRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if (!$user->getRecipe()->contains($recipe)) {
            return $this->json(['message' => "Cette recette n'est pas dans la liste des repas"], Response::HTTP_BAD_REQUEST, []);
        }

        $user->removeRecipe($recipe);

        $userRepository->add($user, true);

        return $this->json(["message" => "Recette supprimée de la liste de repas"], Response::HTTP_OK);
    }
}
