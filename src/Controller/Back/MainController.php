<?php

namespace App\Controller\Back;

use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/back")
 * @IsGranted("ROLE_ADMIN")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/home", name="app_back_main_index")
     */
    public function index(RecipeRepository $recipeRepository, CommentRepository $commentRepository): Response
    {
        $recipeToValidate = count($recipeRepository->findNoValidate());
        $commentToValidate = count($commentRepository->findNoValidate());
        return $this->render('back/main/index.html.twig', [
            'recipeToValidate' => $recipeToValidate,
            'commentToValidate' => $commentToValidate
        ]);
    }
}
