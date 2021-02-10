<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserPasswordChangeType;
use App\Repository\TaskRepository;
use App\Service\User as UserService;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private UserService $userService;
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        UserService $userService,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->encoder = $encoder;
    }

    /**
     * @Route("", name="user")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {
        $users = $this->userRepository->pagination(
            $request->query->getInt('page', 1)
        );

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/add", name="user_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response {
        $user = new User();
        $user->setPassword(md5(time()));

        $form = $this->createForm(UserType::class, $user);
        $form->add('password', TextType::class, [
            'disabled' => true
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "The user has been created.");
            return $this->redirectToRoute('user');
        }

        return $this->render('user/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/edit/{user}", name="user_edit")
     * @param Request $request
     * @param User $user
     * @param UserService $userService
     * @return Response
     */
    public function edit(Request $request, User $user, UserService $userService): Response {

        if($user === $userService->getLoggedUser()) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->flush();

                $this->addFlash('success', "The user has been updated.");
                return $this->redirectToRoute('user');
            }

            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user
            ]);
        } else {
            return $this->redirectToRoute('home');
        }

    }

    /**
     * @Route("/delete/{user}", name="user_delete")
     * @param User $user
     * @return RedirectResponse
     */
    public function delete(User $user): RedirectResponse {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', "The user has been deleted.");
        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/password/change/{user}", name="user_password_change")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function password_change(Request $request, User $user): Response
    {
        if($this->userService->getLoggedUser() === $user) {
            $form = $this->createForm(UserPasswordChangeType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if($this->encoder->isPasswordValid($user, $data['password_current'])) {
                    $user->setPassword($this->encoder->encodePassword($user, $data['password_new']));
                    $this->entityManager->flush();
                    $this->addFlash('success', "Password successfully changed.");
                } else {
                    $this->addFlash('warning', "Your current password is wrong.");
                }
            }
            return $this->render('user/password_change.html.twig', [
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/dashboard/{user}", name="user_dashboard")
     * @param User $user
     * @param UserService $userService
     * @return RedirectResponse|Response
     */
    public function dashboard(User $user, UserService $userService) {
        if($user === $userService->getLoggedUser()) {
            return $this->render('user/dashboard.html.twig', [
                'user' => $user,
            ]);
        } else {
            return $this->redirectToRoute("home");
        }
    }

    /**
     * @Route("/task/{user}", name="user_tasks")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function user_tasks(Request $request, User $user) :Response {
        if($this->userService->getLoggedUser() === $user) {
            $tasks = $this->taskRepository->getUserTasks($user, $request->query->getInt('page', 1));
        } else {
            $this->addFlash('warning', "You are not allowed to access this page.");
            return $this->redirectToRoute('task');
        }

        return $this->render('task/user_tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}