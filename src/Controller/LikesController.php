<?php

namespace App\Controller;

use App\Document\Post;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Document\User as MUser;
use App\Repositories\PostRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/likes")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class LikesController extends AbstractController
{

    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(
        MicroPostRepository $microPostRepository,
        SessionInterface $session,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager,
        AuthorizationCheckerInterface $authorizationChecker,
        FlashBagInterface $flashBag
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->flashBag = $flashBag;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/like/{id}", name="likes_like")
     * @param MicroPost $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function like(MicroPost $post)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }
        $post->like($currentUser);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $this->json([
            'count' => $post->getLikedBy()->count(),
        ], Response::HTTP_OK);
    }
    /**
     * @Route("/unlike/{id}", name="likes_unlike")
     * @param MicroPost $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function unLike(MicroPost $post)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User) {
            return $this->json([], Response::HTTP_OK);
        }
        $post->unLike($currentUser);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $this->json([
            'count' => $post->getLikedBy()->count(),
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/m-like/{id}", name="likes_m_like")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function mLike(Post $post)
    {
        /** @var MUser $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser instanceof MUser) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }
        $post->like($currentUser);
        $this->postRepository->save($post);
        return $this->json([
            'count' => $post->getLikedBy()->count(),
        ], Response::HTTP_OK);
    }
    /**
     * @Route("/m-unlike/{id}", name="likes_m_unlike")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function mUnLike(Post $post)
    {
        /** @var MUser $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser instanceof MUser) {
            return $this->json([], Response::HTTP_OK);
        }
        $post->unLike($currentUser);
        $this->postRepository->save($post);
        return $this->json([
            'count' => $post->getLikedBy()->count(),
        ], Response::HTTP_OK);
    }


}
