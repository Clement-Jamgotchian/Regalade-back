<?php

namespace App\Controller\Back;

use App\Entity\Diet;
use App\Form\DietType;
use App\Repository\DietRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/diet", name="app_back_diet_")
 */
class DietController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(DietRepository $dietRepository): Response
    {
        return $this->render('back/diet/index.html.twig', [
            'diets' => $dietRepository->findBy([], ["createdAt" => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, DietRepository $dietRepository): Response
    {
        $diet = new Diet();
        $form = $this->createForm(DietType::class, $diet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dietRepository->add($diet, true);

            return $this->redirectToRoute('app_back_diet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/diet/new.html.twig', [
            'diet' => $diet,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Diet $diet): Response
    {
        return $this->render('back/diet/show.html.twig', [
            'diet' => $diet,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Diet $diet, DietRepository $dietRepository): Response
    {
        $form = $this->createForm(DietType::class, $diet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dietRepository->add($diet, true);

            return $this->redirectToRoute('app_back_diet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/diet/edit.html.twig', [
            'diet' => $diet,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Diet $diet, DietRepository $dietRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diet->getId(), $request->request->get('_token'))) {
            $dietRepository->remove($diet, true);
        }

        return $this->redirectToRoute('app_back_diet_index', [], Response::HTTP_SEE_OTHER);
    }
}
