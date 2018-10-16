<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 15.10.18
 * Time: 22:24
 */

namespace App\Controller;


use App\Document\User;
use App\Repositories\UserRepository;
use App\Security\TokenGenerator;
use MongoDB\BSON\Timestamp;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MongoTestController extends Controller
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * MongoTestController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        UserRepository $userRepository
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/mongoTest")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function mongoTest()
    {
        $user = new User();
        $user->setEmail("hello1@medium.com")
            ->setFirstName('Dd')
            ->setLastName('Bb')
            ->setPlainPassword("12345678")
            ->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $password = $this->passwordEncoder->encodePassword($user,$user->getPlainPassword());
        $user->setPassword($password);

        $user->setPreferences(['locale'=>'en']);
        $this->userRepository->save($user);
        dump($user);
        die();
        return $this->json(['Status' => 'OK']);
    }
}