<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/user", name="app_api_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, SerializerInterface $serializerInterface, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasherInterface, MemberController $memberController): JsonResponse
    {

        
        /** @var User */
        $newUser = $serializerInterface->deserialize($request->getContent(), User::class, 'json');

        $newUser->setPassword($userPasswordHasherInterface->hashPassword($newUser, $newUser->getPassword()));

        $file = "images/user-picture/" . strtolower($newUser->getNickname()) . '-' . uniqid() . '.png';

        file_put_contents($file, base64_decode($newUser->getPicture()));

        $newUser->setPicture($file);

        $userRepository->add($newUser, true);

        $memberController->add($newUser->getNickname(), $newUser);

        return $this->json($newUser, Response::HTTP_CREATED, [], ["groups" => ["user_browse"]]);
    }

    /**
     * @Route("", name="read", methods={"GET"})
     */
    public function read(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, Response::HTTP_OK, [], ["groups" => ["user_browse"]]);
    }

    /**
     * @Route("", name="edit", methods={"PUT", "PATCH"})
     */
    public function edit(Request $request, SerializerInterface $serializerInterface, UserPasswordHasherInterface $userPasswordHasherInterface, UserRepository $userRepository): JsonResponse
    {
        /** @var User */
        $user = $serializerInterface->deserialize($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $this->getUser()]);

        $user->setPassword($userPasswordHasherInterface->hashPassword($user, $user->getPassword()));

        $userRepository->add($user, true);

        return $this->json($user, Response::HTTP_OK, [], ["groups" => ["user_browse"]]);
    }
}