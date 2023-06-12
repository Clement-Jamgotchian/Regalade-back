<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Repository\RecipeListRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function browse(UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();
        
        $recipes = $user->getRecipeLists();

        return $this->json($recipes, 200, [], ['groups' => ["recipe_browse", "reciplist_browse"]]);

    }

    /**
     * ajouter un repas à la liste
     *
     * @Route("/{id}", name="add", requirements={"id"="\d+"}, methods = {"POST"})
     */
    public function add(?Recipe $recipe, UserRepository $userRepository, UserService $userService, RecipeListRepository $recipeListRepository):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        // TODO A MODIFIER
        if ($user->getRecipe()->contains($recipe)) {
            return $this->json(['message' => "Cette recette est déjà dans la liste des repas"], Response::HTTP_BAD_REQUEST, []);
        }

        $newRecipeList = new RecipeList();
        $newRecipeList->setRecipe($recipe);
        $newRecipeList->setUser($user);

        $recipeListRepository->add($newRecipeList, true);

        return $this->json(["message" => "Recette ajoutée à la liste de repas"], Response::HTTP_CREATED);

    }

    /**
     * supprimer un repas de la liste
     *
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(?Recipe $recipe, UserRepository $userRepository, UserService $userService, RecipeListRepository $recipeListRepository):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        dd($recipeListRepository->findOneByRecipe($recipe, $user));
        $recipeListRepository->find($recipe);

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if (!$user->getRecipe()->contains($recipe)) {
            return $this->json(['message' => "Cette recette n'est pas dans la liste des repas"], Response::HTTP_BAD_REQUEST, []);
        }

        $recipeLists = $user->getRecipeLists();

        foreach ($recipeLists as $list) {
            if ($list->getRecipe() === $recipe);
        }
        $user->removeRecipeList($recipe);

        $userRepository->add($user, true);

        return $this->json(["message" => "Recette supprimée de la liste de repas"], Response::HTTP_OK);
    }

    /**
     * supprimer tous les repas de la liste
     *
     * @Route("", name="deleteAll", methods={"DELETE"})
     */
    public function deleteAll(UserService $userService, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface)
    {
        
        /** @var User */
        $user = $userService->getCurrentUser();

        $recipes = $user->getRecipe();

        foreach ($recipes as $recipe) {
            $user->removeRecipe($recipe);
        }

        $entityManagerInterface->flush();

        return $this->json(["message" => "Liste de repas purgée"], Response::HTTP_OK);
    }
}
