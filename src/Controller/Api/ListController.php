<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Repository\RecipeListRepository;
use App\Services\AddEditDeleteService;
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
    public function browse(Request $request, PaginatorInterface $paginatorInterface):JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
        
        $recipesListCollection = $user->getRecipeLists();
        $recipesList = $recipesListCollection->getValues();

        $recipesWithPagination = $paginatorInterface->paginate(
            $recipesList,
            $request->query->getInt('page', 1),
            12
        );

        $toSend = [];
        $toSend['totalPages'] = ceil($recipesWithPagination->getTotalItemCount() / $recipesWithPagination->getItemNumberPerPage());
        $toSend['recipesList'] = $recipesWithPagination;

        return $this->json($toSend, 200, [], ['groups' => ["recipe_browse", "recipeList_browse"]]);

    }

    /**
     * ajouter un repas à la liste
     *
     * @Route("/{id}", name="add", requirements={"id"="\d+"}, methods = {"POST"})
     */
    public function add(?Recipe $recipe, RecipeListRepository $recipeListRepository):JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
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
        $newRecipeList->setPortions(count($user->getMembers()));

        $recipeListRepository->add($newRecipeList, true);

        return $this->json(["message" => "Recette ajoutée à la liste de repas"], Response::HTTP_CREATED);

    }

    /**
     * supprimer un repas de la liste
     *
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(?Recipe $recipe, RecipeListRepository $recipeListRepository):JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

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

       /**
     * éditer un repas de la liste
     *
     * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
     */
    public function edit(?Recipe $recipe,AddEditDeleteService $addEditDeleteService, RecipeListRepository $recipeListRepository):JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $editRecipe = $recipeListRepository->findOneByRecipe($recipe, $user);

        $editedRecipe = $addEditDeleteService->edit($editRecipe, $recipeListRepository, RecipeList::class);

        return $this->json($editedRecipe, 200, [], ['groups' => ["recipe_browse", "recipeList_browse"]]);

    }
    
}
