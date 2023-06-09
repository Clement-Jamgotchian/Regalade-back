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
    private $addEditDeleteService;
    private $memberRepository;

    public function __construct(AddEditDeleteService $addEditDeleteService, MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
        $this->addEditDeleteService = $addEditDeleteService;
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


}
