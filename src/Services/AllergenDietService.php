<?php

namespace App\Services;

use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class AllergenDietService
{
    private $request;
    private $allergenRepository;
    private $dietRepository;
    private $recipeRepository;

    public function __construct(RequestStack $request, AllergenRepository $allergenRepository, DietRepository $dietRepository, RecipeRepository $recipeRepository)
    {
        $this->request = $request->getCurrentRequest();
        $this->allergenRepository = $allergenRepository;
        $this->dietRepository = $dietRepository;
        $this->recipeRepository = $recipeRepository;
    }

    public function hideRecipesWithAllergen()
    {
        $allergenRecipes = [];
        if(!is_null($this->request->query->get('allergen')))
        {

            /** @var array */
            $allergens = $this->request->query->get('allergen');
            foreach ($allergens as $allergenId) {
                if ($this->allergenRepository->find($allergenId) === null) {
                    return false;
                }
                $concernAllergen = $this->allergenRepository->find($allergenId);
                $associatedRecipes = $concernAllergen->getRecipe();

                foreach ($associatedRecipes as $recipe) {
                    $allergenRecipes[] = $recipe;
                }
            }
        }

        return $allergenRecipes;

    }

    public function hideRecipesWithoutDiet()
    {
        $noDietRecipes = [];
        if(!is_null($this->request->query->get('diet')))
        {

            /** @var array */
            $diets = $this->request->query->get('diet');
            foreach ($diets as $dietId) {
                if ($this->dietRepository->find($dietId) === null) {
                    return false;
                }
                $concernDiet = $this->dietRepository->find($dietId);

                foreach($this->recipeRepository->findAll() as $recipe)
                {
                    if (!$concernDiet->getRecipe()->contains($recipe))
                    {
                        $noDietRecipes[] = $recipe;
                    }
                }
            }
        }

        return $noDietRecipes;
    }
}