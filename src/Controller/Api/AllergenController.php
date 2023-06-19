<?php

namespace App\Controller\Api;

use App\Entity\Allergen;
use App\Repository\AllergenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/allergens", name="app_api_allergens_")
 */
class AllergenController extends AbstractController
{

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, AllergenRepository $allergenRepository): JsonResponse
    {
        if(!is_null($request->query->get('search'))) {
            $allergens = $allergenRepository->findWhere($request->query->get('search'));
        } else {
            $allergens = $allergenRepository->findAll();
        }

        if (empty($allergens)) {
            return $this->json('', Response::HTTP_NO_CONTENT, []);
        }

        return $this->json($allergens, 200, [], ['groups' => ["allergen_browse"]]);
    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Allergen $allergen): JsonResponse
    {
        if ($allergen === null) {
            return $this->json(['message' => "Cette allergie n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($allergen, 200, [], ['groups' => ["allergen_browse"]]);
    }
}
