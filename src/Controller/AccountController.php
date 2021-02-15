<?php

namespace App\Controller;

use App\Form\UserPasswordChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountController
 * @package App\Controller
 * @Route("/account")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class AccountController extends AbstractController
{
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager
    ) {

        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("", name="account_dashboard")
     * @return Response
     */
    public function dashboard(): Response {
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/password", name="account_password")
     * @param Request $request
     * @return Response
     */
    public function password(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserPasswordChangeType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($this->encoder->isPasswordValid($user, $data['password_current'])) {
                $user->setPassword(
                    $this->encoder->encodePassword($user, $data['password_new'])
                );
                $this->entityManager->flush();
                $this->addFlash('success', "Password successfully changed.");
            } else {
                $this->addFlash('warning', "Your current password is wrong.");
            }
        }
        return $this->render('user/password_change.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}