<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\UserRegisterEvent;
use App\Form\LoginType;
use App\Form\SignUpType;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(UserRepository $userRepository,
                                SessionInterface $session,
                                EntityManagerInterface $entityManager,
                                AuthenticationUtils $authenticationUtils,
                                UserPasswordEncoderInterface $passwordEncoder,
                                EventDispatcherInterface $eventDispatcher,
                                TokenGenerator $tokenGenerator,
                                FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->authenticationUtils = $authenticationUtils;
        $this->passwordEncoder = $passwordEncoder;
        $this->flashBag = $flashBag;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginType::class,['username' =>$lastUsername]);

        return $this->render('auth/login.html.twig',[
            'form' => $form->createView(),
            'error' => $lastError
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \RuntimeException('This should never be called directly');
    }

    /**
     * @Route("/register", name="security_register", methods={"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);
            $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userRegisterEvent = new UserRegisterEvent($user);
            $this->eventDispatcher->dispatch(UserRegisterEvent::NAME, $userRegisterEvent);

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('auth/register.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{token}", name="security_confirm", methods={"GET"})
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirm(string $token)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken'=>$token]);
        if($user !== null) {
            $user->setEnabled(true)
                ->setConfirmationToken('');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return $this->render('auth/confirm.html.twig',[
            'user' => $user,
        ]);
    }
}
