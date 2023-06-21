<?php

namespace App\Controller\Api;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/departments", name="app_api_departments_")
 */
class DepartmentController extends AbstractController
{

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, DepartmentRepository $departmentRepository): JsonResponse
    {
        if(!is_null($request->query->get('search'))) {
            $departments = $departmentRepository->findWhere($request->query->get('search'));
        } else {
            $departments = $departmentRepository->findAll();
        }

        if (empty($departments)) {
            return $this->json('', Response::HTTP_NO_CONTENT, []);
        }

        return $this->json($departments, 200, [], ['groups' => ["department_browse"]]);
    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Department $department): JsonResponse
    {
        if ($department === null) {
            return $this->json(['message' => "Ce rayon n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($department, 200, [], ['groups' => ["department_browse"]]);
    }
}
