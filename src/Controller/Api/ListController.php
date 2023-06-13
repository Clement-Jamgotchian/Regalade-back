<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Repository\RecipeListRepository;
use App\Repository\UserRepository;
use App\Services\AddEditDeleteService;
use App\Services\UserService;
use Knp\Component\Pager\PaginatorInterface;
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
    public function browse(UserService $userService, Request $request, PaginatorInterface $paginatorInterface):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();
        
        $recipesList = $user->getRecipeLists();

        $recipes = [];
        foreach ($recipesList as $recipeListElement) {
            $recipes[] = $recipeListElement->getRecipe();
        }

        $recipesWithPagination = $paginatorInterface->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            12
        );

        $toSend = [];
        $toSend['totalPages'] = ceil($recipesWithPagination->getTotalItemCount() / $recipesWithPagination->getItemNumberPerPage());
        $toSend['recipes'] = $recipesWithPagination;

        return $this->json($toSend, 200, [], ['groups' => ["recipe_browse", "reciplist_browse"]]);

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
        $addRecipe = $recipeListRepository->findOneByRecipe($recipe, $user);

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if ($user->getRecipeLists()->contains($addRecipe)) {
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

        $rmRecipe = $recipeListRepository->findOneByRecipe($recipe, $user);
      
        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        if ($rmRecipe) {
            $recipeListRepository->remove($rmRecipe, true);
                return $this->json(["message" => "Recette supprimée de la liste de repas"], Response::HTTP_OK);
        }
        else
        {
            return $this->json(['message' => "Cette recette n'est pas dans la liste des repas"], Response::HTTP_BAD_REQUEST, []);
        }

    }

    /**
     * supprimer tous les repas de la liste
     *
     * @Route("", name="deleteAll", methods={"DELETE"})
     */
    public function deleteAll(AddEditDeleteService $addEditDeleteService, RecipeListRepository $recipeListRepository): JsonResponse
    {
        $addEditDeleteService->deleteAll(RecipeList::class, $recipeListRepository);

        return $this->json(["message" => "Toutes les recettes ont été supprimée de la liste des repas"], Response::HTTP_OK);
    }
    
}
