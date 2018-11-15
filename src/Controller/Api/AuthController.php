<?php

namespace App\Controller\Api;

use App\Document\User;
use App\Document\UserPreferences;
use App\Events\UserRegisterEvent;
use App\Form\Api\RegisterType;
use App\Repositories\UserRepository;
use App\Security\TokenGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthController
 * @package App\Controller\Api
 * @Route("/api/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $eventDispatcher,
        TokenGenerator $tokenGenerator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
    }


    /**
     * @Route("/register", name="api_register")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function register(Request $request)
    {
        header('Content-Type: cli');
        $response = false;
        $message = 'Fail';
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        if ('POST' === $request->getMethod()) {
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if($form->isValid()) {
                try{
                    $password = $this->passwordEncoder->encodePassword($user,$user->getPlainPassword());
                    $user->setPassword($password);
                    $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());

                    $userPreferences = new UserPreferences();
                    $user->setPreferences($userPreferences);

                    $this->userRepository->save($user);
                    $userRegisterEvent = new UserRegisterEvent($user);
                    $this->eventDispatcher->dispatch(UserRegisterEvent::NAME, $userRegisterEvent);
                    $response = true;
                    $message = 'OK';
                }catch (\Exception $e) {
                    $message = $e->getMessage();
                }

            }else{
                //$message = (string) $form->getErrors(true, false);
                $message = $this->getErrorMessages($form);
            }
        }
        return $this->json(['status'=>$response, 'message'=>$message], Response::HTTP_OK);
    }


    /**
     * get Error Messages From Form.
     * @param \Symfony\Component\Form\FormInterface $form
     * @return array
     */
    protected function getErrorMessages(\Symfony\Component\Form\FormInterface $form) {
        $errors = array();
        if ($form->count() > 0) {
            foreach ($form->all() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = (String) $form[$child->getName()]->getErrors();
                }
            }
        }
        return $errors;
    }
    /**
     * @Route("/confirm/{confirmationToken}", name="api_security_confirm", methods={"GET"})
     * @param string $confirmationToken
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirm(string $confirmationToken)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['confirmationToken'=>$confirmationToken]);
        if($user !== null) {
            try{
                $user->setEnabled(true)
                    ->setConfirmationToken('');
                $this->userRepository->save($user);
                $response = true;
                $message = $user->getFirstName();
            }catch (\Exception $exception) {
                $response = false;
                $message = $exception->getMessage();
            }

        } else {
            $response = false;
            $message = 'Something Wrong. Check your email';
        }
        return $this->json(['status'=>$response, 'message'=>$message], Response::HTTP_OK);
    }
}
