<?php

namespace App\Controller\Back;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\ContainsIngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/recipe")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("/", name="app_back_recipe_index", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('back/recipe/index.html.twig', [
            'recipes' => $recipeRepository->findNoValidate(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_recipe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RecipeRepository $recipeRepository): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeRepository->add($recipe, true);

            return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_recipe_show", methods={"GET"})
     */
    public function show(Recipe $recipe): Response
    {
        return $this->render('back/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_recipe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Recipe $recipe, RecipeRepository $recipeRepository, ContainsIngredientRepository $containsIngredientRepository): Response
    {
        $originalContains = new ArrayCollection();
        foreach($recipe->getContainsIngredients() as $contains)
        {
            $originalContains->add($contains);
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form);
            // TODO Gerer suppression contains pour Edit
            dd($recipe);
            foreach ($originalContains as $contains) {
                
                if (false === $recipe->getContainsIngredients()->contains($contains))
                {
                    $recipe->removeContainsIngredient($contains);
                }
            }

            $recipeRepository->add($recipe, true);

            return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_recipe_delete", methods={"POST"})
     */
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/validate", name="app_back_recipe_validate", methods={"GET"})
     */
    public function validate(Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        $recipe->setIsValidate(true);
        $recipeRepository->add($recipe, true);

        return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
