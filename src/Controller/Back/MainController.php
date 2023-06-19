<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
    * @Route("/admin", name="app_back_main_")
    */
    public function browse(UserRepository $userRepository,RecipeRepository  $recipeRepository, CommentRepository $commentRepository)
    {
        $comments = $commentRepository->findAll();
        $recipes = $recipeRepository->findAll();
        $users = $userRepository->findAll();
        dd($comments);

        return $this->render('back/index.html.twig', [
            'comments' => $comments,
            'recipe' => $recipes,
            'user' => $users,
            
        ]);

    }
}
