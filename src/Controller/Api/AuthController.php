<?php

namespace App\Controller\Api;

use App\Document\Post;
use App\Document\User;
use App\Form\Api\RegisterType;
use App\Form\PostType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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

    public function __construct(
        UserRepository $userRepository,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->userRepository = $userRepository;
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
                    $this->userRepository->save($user);
                    $response = true;
                    $message = 'OK';
                }catch (\Exception $e) {
                    $message = $e->getMessage();
                }

            }else{
                $message = (string) $form->getErrors(true, false);
            }
        }
        return $this->json(['status'=>$response, 'message'=>$message], Response::HTTP_OK);
    }
}
