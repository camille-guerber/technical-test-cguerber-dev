<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\Filter\TaskFilterType;
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
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
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
     * @return Response
     */
    public function edit(Request $request, User $user): Response {

        if($user === $this->getUser()) {
            // TODO : Redict to the profil page
            //return $this->redirectToRoute('profil');
        }

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
     * @param User $user
     * @return Response
     * @Route("/tasks/{user}", name="user_tasks")
     */
    public function userTasks(User $user): Response {

        return $this->render('user/user_tasks.html.twig', [
            'tasks' => $user->getTasks(),
            'user' => $user,
        ]);
    }

}