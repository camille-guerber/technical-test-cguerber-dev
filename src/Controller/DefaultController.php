<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Service\Statistic;
use App\Service\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    private AuthenticationUtils $authenticationUtils;
    private TaskRepository $taskRepository;

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        TaskRepository $taskRepository
    )

    {
        $this->authenticationUtils = $authenticationUtils;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("", name="home")
     * @return Response
     */
    public function index(): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $stats = null;

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $stats = [
                'opened' => $this->taskRepository->count([
                    'closed' => false
                ]),
                'unassigned' => $this->taskRepository->count([
                    'user' => false
                ]),
                'owned' => $this->taskRepository->count([
                    'user' => $this->getUser()
                ])
            ];
        }

        return $this->render('default/index.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername,
            'stats' => $stats,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}