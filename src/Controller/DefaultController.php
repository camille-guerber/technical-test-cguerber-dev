<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    private AuthenticationUtils $authenticationUtils;
    private TaskRepository $taskRepository;
    private const CACHE_EXPIRATION = 60;

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
        $cache = new FilesystemAdapter();

        $error = $this->authenticationUtils->getLastAuthenticationError();

        $lastUsername = $this->authenticationUtils->getLastUsername();

        $stats = null;

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {

            // The callable function will only be executed on a cache miss
            $stats = $cache->get('stats', function (ItemInterface $item) {
                $item->expiresAfter(self::CACHE_EXPIRATION);

                $stats = [
                    'opened' => $this->taskRepository->openedTasks(),
                    'unassigned' => $this->taskRepository->unassignedTasks(),
                    'owned' => $this->taskRepository->ownedTasks($this->getUser())
                ];

                return $stats;
            });
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