<?php

namespace App\Controller\Back;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/department", name="app_back_department_")
 * @IsGranted("ROLE_ADMIN")
 */
class DepartmentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(DepartmentRepository $departmentRepository): Response
    {
        return $this->render('back/department/index.html.twig', [
            'departments' => $departmentRepository->findBy([], ["createdAt" => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, DepartmentRepository $departmentRepository): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departmentRepository->add($department, true);

            return $this->redirectToRoute('app_back_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/department/new.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Department $department): Response
    {
        return $this->render('back/department/show.html.twig', [
            'department' => $department,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Department $department, DepartmentRepository $departmentRepository): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departmentRepository->add($department, true);

            return $this->redirectToRoute('app_back_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/department/edit.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Department $department, DepartmentRepository $departmentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$department->getId(), $request->request->get('_token'))) {
            $departmentRepository->remove($department, true);
        }

        return $this->redirectToRoute('app_back_department_index', [], Response::HTTP_SEE_OTHER);
    }
}
