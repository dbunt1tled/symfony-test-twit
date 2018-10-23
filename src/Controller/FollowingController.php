<?php

namespace App\Controller;

use App\Entity\User;
use App\Document\User as MUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/following")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class FollowingController extends AbstractController
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var \App\Repositories\UserRepository
     */
    private $userRepositoryM;

    public function __construct(
        UserRepository $userRepository,
        \App\Repositories\UserRepository $userRepositoryM,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        AuthorizationCheckerInterface $authorizationChecker,
        FlashBagInterface $flashBag
    )
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->flashBag = $flashBag;
        $this->userRepositoryM = $userRepositoryM;
    }

    /**
     * @Route("/follow/{id}", name="following_follow")
     * @param User $userToFollow
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function follow(User $userToFollow)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if ($userToFollow !== $currentUser) {
            $currentUser->follow($userToFollow);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('micro_post_user',['username'=>$userToFollow->getUsername()]);
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     * @param User $userToUnFollow
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unFollow(User $userToUnFollow)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if ($userToUnFollow !== $currentUser) {
            $currentUser->getFollowing()->removeElement($userToUnFollow);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('micro_post_user',['username'=>$userToUnFollow->getUsername()]);
    }

    /**
     * @Route("/m-follow/{id}", name="following_m_follow")
     * @param MUser $userToFollow
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function mFollow(MUser $userToFollow)
    {
        /**
         * @var MUser $currentUser
         */
        $currentUser = $this->getUser();
        if ($userToFollow !== $currentUser) {
            $currentUser->follow($userToFollow);
            $this->userRepositoryM->save($currentUser);
        }

        return $this->redirectToRoute('post_user',['email'=>$userToFollow->getUsername()]);
    }

    /**
     * @Route("/m-unfollow/{id}", name="following_m_unfollow")
     * @param MUser $userToUnFollow
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mUnFollow(MUser $userToUnFollow)
    {
        /**
         * @var MUser $currentUser
         */
        $currentUser = $this->getUser();
        if ($userToUnFollow !== $currentUser) {
            $currentUser->getFollowing()->removeElement($userToUnFollow);
            $this->userRepositoryM->save($currentUser);
        }
        return $this->redirectToRoute('post_user',['email'=>$userToUnFollow->getUsername()]);
    }

}
