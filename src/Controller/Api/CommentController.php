<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Services\AddEditDeleteService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/comments", name="app_api_comments_")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(AddEditDeleteService $addEditDeleteService, CommentRepository $commentRepository): JsonResponse
    {

        $newComment = $addEditDeleteService->add($commentRepository, Comment::class);

        return $this->json($newComment, 200, [], ['groups' => ["comment_read"]]);
    }
}
