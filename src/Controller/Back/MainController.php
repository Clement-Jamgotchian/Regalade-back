<?php

namespace App\Controller\Back;

use App\Repository\AllergenRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\DepartmentRepository;
use App\Repository\DietRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/back", name="app_back_main_")
 * @IsGranted("ROLE_ADMIN")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/home", name="index")
     */
    public function index(RecipeRepository $recipeRepository, CommentRepository $commentRepository, UserRepository $userRepository, IngredientRepository $ingredientRepository, CategoryRepository $categoryRepository, DepartmentRepository $departmentRepository, AllergenRepository $allergenRepository, DietRepository $dietRepository): Response
    {
        $recipeToValidate = count($recipeRepository->findBy(['isValidate' => null]));
        $commentToValidate = count($commentRepository->findBy(['isValidate' => null]));
        $users = count($userRepository->findAll());
        $ingredients = count($ingredientRepository->findAll());
        $categories = count($categoryRepository->findAll()); 
        $departments = count($departmentRepository->findAll());
        $allergens = count($allergenRepository->findAll());
        $diets = count($dietRepository->findAll());

        $lastRecipe = $recipeRepository->findOneBy([],["createdAt" => 'DESC']);
        $lastRecipeTime = date_diff($lastRecipe->getCreatedAt(), new DateTime('now'))->format('%d jour(s) - %H heure(s) - %I minute(s)');

        $lastComment = $commentRepository->findOneBy([],["createdAt" => 'DESC']);
        $lastCommentTime = date_diff($lastComment->getCreatedAt(), new DateTime('now'))->format('%d jour(s) - %H heure(s) - %I minute(s)');

        $lastUser = $userRepository->findOneBy([],["createdAt" => 'DESC']);
        $lastUserTime = date_diff($lastUser->getCreatedAt(), new DateTime('now'))->format('%d jour(s) - %H heure(s) - %I minute(s)');

        return $this->render('back/main/index.html.twig', [
            'recipeToValidate' => $recipeToValidate,
            'commentToValidate' => $commentToValidate,
            'lastRecipeTime' => $lastRecipeTime,
            'lastCommentTime' => $lastCommentTime,
            'lastUserTime' => $lastUserTime,
            'users' => $users,
            'ingredients' => $ingredients,
            'categories' => $categories,
            'departments' => $departments,
            'allergens' => $allergens,
            'diets' => $diets
        ]);
    }
}
