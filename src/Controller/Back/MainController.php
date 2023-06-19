<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/admin", name="app_back_main_")
*/
class MainController extends AbstractController
{
    /**
    * @Route("", name="browse", methods{"GET"})
    */
    public function browse(UserRepository $userRepository,RecipeRepository  $recipeRepository, CommentRepository $commentRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $comments = $commentRepository->findAll();
        $recipes = $recipeRepository->findAll();
        $users = $userRepository->findAll();

        return $this->render('main/index.html.twig', [
            'comments' => $comments,
            'recipe' => $recipes,
            'user' => $users,
            
        ]);

    }
}
