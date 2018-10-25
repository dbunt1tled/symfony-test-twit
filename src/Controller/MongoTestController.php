<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 15.10.18
 * Time: 22:24
 */

namespace App\Controller;


use App\Document\Category;
use App\Document\LikeNotification;
use App\Document\Post;
use App\Document\User;
use App\Repositories\CategoryRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PostRepository;
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
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * MongoTestController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     * @param UserRepository $userRepository
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        UserRepository $userRepository,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        NotificationRepository $notificationRepository
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->notificationRepository = $notificationRepository;
        $this->categoryRepository = $categoryRepository;
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
    /**
     * @Route("/mongoTest1")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function mongoTest1()
    {
        $post = new Post();
        $post->setText("1111111111111111111111111111")
            ->setTitle('Privet.Go KS')
            ->setUser($this->getUser());
        $this->postRepository->save($post);
        dump($post);
        die();
        return $this->json(['Status' => 'OK']);
    }

    /**
     * @Route("/mongoTest2")
     */
    public function mongoTest2()
    {
        $post = $this->postRepository->findOneById('5bcd88d54c21bb5408640f80');
        $notification = new LikeNotification();
        $notification->setUser($post->getUser())
            ->setPost($post)
            ->setLikedBy($this->getUser());
        $this->notificationRepository->save($notification);
    }

    /**
     * @Route("/mongoTest3")
     */
    public function mongoTest3()
    {
        $posts = iterator_to_array($this->postRepository->getPosts());
        $food = new Category();
        $food->setTitle('Еда');
        for ( $i = 0; $i < 3; $i++) {
            $food->addPost($posts[array_rand($posts)]);
        }
        $this->categoryRepository->save($food);
        $fruits = new Category();
        $fruits->setTitle('Фрукты');
        $fruits->setParent($food);
        for ( $i = 0; $i < 3; $i++) {
            $fruits->addPost($posts[array_rand($posts)]);
        }
        $this->categoryRepository->save($fruits);
        dump($fruits->getPosts());
        die("\n");

    }
    /**
     * @Route("/mongoTest4")
     */
    public function mongoTest4()
    {
        $category = $this->categoryRepository->childrenHierarchy();
    }
}