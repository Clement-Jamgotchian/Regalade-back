<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Entity\User;
use App\Repository\MemberRepository;
use App\Services\AddEditDeleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/members", name="app_api_members_")
 */
class MemberController extends AbstractController
{
    private $addEditDeleteService;
    private $memberRepository;

    public function __construct(AddEditDeleteService $addEditDeleteService, MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
        $this->addEditDeleteService = $addEditDeleteService;
    }

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $members = $user->getMembers();

        return $this->json($members, 200, [], ['groups' => ["member_browse"]]);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add($nickname = null, $newUser = null)
    {
        $newMember = $this->addEditDeleteService->add($this->memberRepository, Member::class, $newUser);

        if ($nickname === null) {
            return $this->json($newMember, 200, [], ['groups' => ["member_browse"]]);
        }
    }

    /**
     * @Route("/{id}", name="read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(?Member $member): JsonResponse
    {
        if ($member === null)
        {
            return $this->json(['message' => "Ce membre n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        return $this->json($member, 200, [], ['groups' => ["member_browse", "member_read"]]);
    }

    /**
     * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT", "PATCH"})
     * 
     */
    public function edit(?Member $member): JsonResponse
    {

        if ($member === null)
        {
            return $this->json(['message' => "Ce membre n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }

        $editedMember = $this->addEditDeleteService->edit($member, $this->memberRepository, Member::class);

        return $this->json($editedMember, 200, [], ['groups' => ["member_browse", "member_read"]]);
    }

    /**
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     * 
     */
    public function delete(?Member $member): JsonResponse
    {

        if ($member === null)
        {
            return $this->json(['message' => "Ce membre n'existe pas"], Response::HTTP_NOT_FOUND, []);
        }
        
        $deletedMember = $this->addEditDeleteService->delete($member, $this->memberRepository, Member::class);

        return $this->json(["message" => $deletedMember[0]], $deletedMember[1]);

    }

}
