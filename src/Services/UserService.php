<?php

namespace App\Services;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{

    /** @var  TokenStorageInterface */
    private $tokenStorage;

    private $currentUser;

    /**
     * @param TokenStorageInterface  $storage
     */
    public function __construct(TokenStorageInterface $storage)
    {
        $this->tokenStorage = $storage;
    }

    public function getCurrentUser() {
        $token = $this->tokenStorage->getToken();
        if ($token && is_null($this->currentUser)) {
          $this->currentUser = $token->getUser();
        }
    
        return $this->currentUser;
      }
    
}