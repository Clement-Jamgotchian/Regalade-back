<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/api/favorite", name="app_api_favorite_")
 */
class FavoriteController extends AbstractController
{
    /**
     *  afficher la liste des favoris
     *
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(UserService $userService, Request $request, PaginatorInterface $paginatorInterface):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();
        
        $recipes = $user->getFavoriteRecipes();

        $recipesWithPagination = $paginatorInterface->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            12
        );

        $toSend = [];
        $toSend['totalPages'] = ceil($recipesWithPagination->getTotalItemCount() / $recipesWithPagination->getItemNumberPerPage());
        $toSend['recipes'] = $recipesWithPagination;

        return $this->json($toSend, 200, [], ['groups' => ["recipe_browse"]]);

    }

    /**
     * ajouter un favoris à la liste
     *
     * @Route("/{id}", name="add", requirements={"id"="\d+"}, methods = {"POST"})
     */
    public function add(?Recipe $recipe, UserRepository $userRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if ($user->getFavoriteRecipes()->contains($recipe)) {
            return $this->json(['message' => "Cette recette est déjà dans la liste des favoris"], Response::HTTP_BAD_REQUEST, []);
        }

        $user->addFavoriteRecipe($recipe);

        $userRepository->add($user, true);

        return $this->json(["message" => "Recette ajoutée à la liste de favoris"], Response::HTTP_CREATED);

    }

    /**
     * supprimer un favoris de la liste
     *
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(?Recipe $recipe, UserRepository $userRepository, UserService $userService):JsonResponse
    {

        /** @var User */
        $user = $userService->getCurrentUser();

        if ($recipe === null) {
            return $this->json(['message' => "Cette recette n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        if (!$user->getFavoriteRecipes()->contains($recipe)) {
            return $this->json(['message' => "Cette recette n'est pas dans la liste des favoris"], Response::HTTP_BAD_REQUEST, []);
        }

        $user->removeFavoriteRecipe($recipe);

        $userRepository->add($user, true);

        return $this->json(["message" => "Recette supprimée de la liste de favoris"], Response::HTTP_OK);
    }

    /**
     * supprimer tous les favoris de la liste
     *
     * @Route("", name="deleteAll", methods={"DELETE"})
     */
    public function deleteAll(UserService $userService, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface)
    {
        
        /** @var User */
        $user = $userService->getCurrentUser();

        $recipes = $user->getFavoriteRecipes();

        foreach ($recipes as $recipe) {
            $user->removeFavoriteRecipe($recipe);
        }

        $entityManagerInterface->flush();

        return $this->json(["message" => "Liste de favoris purgée"], Response::HTTP_OK);
    }
}