<?php

namespace App\Controller\Back;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\ContainsIngredientRepository;
use App\Repository\RecipeRepository;
use App\Services\UploadImageService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/back/recipe",name="app_back_recipe_")
 * @IsGranted("ROLE_ADMIN")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('back/recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/validation", name="validation", methods={"GET"})
     */
    public function browse(RecipeRepository $recipeRepository): Response
    {
        return $this->render('back/recipe/validation.html.twig', [
            'recipes' => $recipeRepository->findBy(['isValidate' => null]),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, RecipeRepository $recipeRepository, UploadImageService $uploadImageService): Response
    {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!is_null($recipe->getPicture())) {

                $recipe->setPicture("data:image/png;base64," . base64_encode($recipe->getPicture()));

                $recipe = $uploadImageService->upload($recipe);
            }

            $recipeRepository->add($recipe, true);

            return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Recipe $recipe): Response
    {
        return $this->render('back/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
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

            foreach ($originalContains as $contains) {
                
                if (false === $recipe->getContainsIngredients()->contains($contains))
                {
                    $containsIngredientRepository->remove($contains, true);
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
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/validate", name="validate", methods={"GET"})
     */
    public function validate(Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        $recipe->setIsValidate(true);
        $recipeRepository->add($recipe, true);

        return $this->redirectToRoute('app_back_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
