<?php

namespace App\Services;

use App\Repository\RecipeRepository;

class UpdateRatingService
{
    private $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function update($repository, $recipe, $newRating) {

        $comments = $repository->findBy(["recipe" => $recipe]);

        $rating = [];
        foreach ($comments as $comment) {
            $rating[] = $comment->getRating();
        }

        $rating[] = $newRating;

        $sum = array_sum($rating);
        $newRating = round($sum / count($rating), 1);

        $recipeToUpdate = $this->recipeRepository->find($recipe);

        $recipeToUpdate->setRating($newRating);

        $this->recipeRepository->add($recipeToUpdate, true);
    }
}