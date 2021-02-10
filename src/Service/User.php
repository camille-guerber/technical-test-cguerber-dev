<?php


namespace App\Service;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class User
{
    /**
     * @var TaskRepository
     */
    private UserRepository $userRepository;

    /**
     * @var Security
     */
    private Security $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @return UserInterface|null
     */
    public function getLoggedUser()
    {
        return $this->security->getUser();
    }

    /**
     * @return string|null
     */
    public function getCurrentPassword()
    {
        return $this->security->getUser()->getPassword();
    }
}