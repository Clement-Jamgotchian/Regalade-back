<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/categories", name="app_api_categories_")
 */
class CategoryController extends AbstractController
{

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(CategoryRepository $categoryRepository): JsonResponse
    {
        return $this->json($categoryRepository->findAll(), 200, [], ['groups' => ["category_browse"]]);
    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Category $category): JsonResponse
    {
        if ($category === null) {
            return $this->json(['message' => "Cette catégorie n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($category, 200, [], ['groups' => ["category_browse"]]);
    }
}
