<?php

namespace App\Controller;

use App\Repository\TaskRepository;
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
    /**
     * @var TaskRepository
     */
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
     */
    public function index(): Response
    {
        $stats = [
            'opened' => 0,
            'unassigned' => 0,
            'owned' => 0
        ];

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        if($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $stats['opened'] = count($this->taskRepository->getOpenedTasks());

            $stats['unassigned'] = count($this->taskRepository->getUnassignedTasks());

            $stats['owned'] = count($this->taskRepository->getOwnedOpenedTasks());
        }

        return $this->render('default/index.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername,
            'stats' => $stats
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}