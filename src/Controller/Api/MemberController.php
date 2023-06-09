<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Services\AddEditDeleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/members", name="app_api_member_")
 */
class MemberController extends AbstractController
{
    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(AddEditDeleteService $addEditDeleteService, MemberRepository $memberRepository): JsonResponse
    {
        $newMember = $addEditDeleteService->add($memberRepository, Member::class);

        return $this->json($newMember, 200, [], ['groups' => ["member_browse"]]);
    }
}
