<?php

namespace App\Controller\Back;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/back/comment")
 * @IsGranted("ROLE_ADMIN")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("", name="app_back_comment_index")
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('back/comment/index.html.twig', [
            'comments' => $commentRepository->findNoValidate()
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('back/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_back_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/validate", name="app_back_comment_validate", methods={"GET"})
     */
    public function validate(Comment $comment, CommentRepository $commentRepository): Response
    {
        $comment->setIsValidate(true);
        $commentRepository->add($comment, true);

        return $this->redirectToRoute('app_back_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
