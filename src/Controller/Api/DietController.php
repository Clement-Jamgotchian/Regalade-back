<?php

namespace App\Controller\Api;

use App\Entity\Diet;
use App\Repository\DietRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/diets", name="app_api_diets_")
 */
class DietController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, DietRepository $dietRepository): JsonResponse
    {
        $diets = (!is_null($request->query->get('search'))) ? $dietRepository->findWhere($request->query->get('search'))
                                                            : $dietRepository->findAll();

        if (empty($diets)) {
            return $this->json('', Response::HTTP_NO_CONTENT, []);
        }

        return $this->json($diets, 200, [], ['groups' => ["diet_browse"]]);
    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Diet $diet): JsonResponse
    {
        if ($diet === null) {
            return $this->json(['message' => "Ce régime alimentaire n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($diet, 200, [], ['groups' => ["diet_browse"]]);
    }
}
