<?php


namespace App\Controller;


use App\Entity\User;
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

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
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
     * @Route("/profile/{user}", name="user_profile")
     * @param User $user
     */
    public function profile(User $user, UserService $userService) {
        if($user === $userService->getLoggedUser()) {
            return $this->render('user/profile.html.twig', [
                'user' => $user,
            ]);
        } else {
            return $this->redirectToRoute("home");
        }
    }
}