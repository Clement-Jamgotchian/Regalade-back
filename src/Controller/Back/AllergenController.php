<?php

namespace App\Controller\Back;

use App\Entity\Allergen;
use App\Form\AllergenType;
use App\Repository\AllergenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/back/allergen", name="app_back_allergen_")
 * @IsGranted("ROLE_ADMIN")
 */
class AllergenController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(AllergenRepository $allergenRepository): Response
    {
        return $this->render('back/allergen/index.html.twig', [
            'allergens' => $allergenRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, AllergenRepository $allergenRepository): Response
    {
        $allergen = new Allergen();
        $form = $this->createForm(AllergenType::class, $allergen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $allergenRepository->add($allergen, true);

            return $this->redirectToRoute('app_back_allergen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/allergen/new.html.twig', [
            'allergen' => $allergen,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Allergen $allergen): Response
    {
        return $this->render('back/allergen/show.html.twig', [
            'allergen' => $allergen,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Allergen $allergen, AllergenRepository $allergenRepository): Response
    {
        $form = $this->createForm(AllergenType::class, $allergen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $allergenRepository->add($allergen, true);

            return $this->redirectToRoute('app_back_allergen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/allergen/edit.html.twig', [
            'allergen' => $allergen,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Allergen $allergen, AllergenRepository $allergenRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$allergen->getId(), $request->request->get('_token'))) {
            $allergenRepository->remove($allergen, true);
        }

        return $this->redirectToRoute('app_back_allergen_index', [], Response::HTTP_SEE_OTHER);
    }
}
