<?php

namespace App\Controller\Api;


use App\Document\User;
use App\Helpers\HttpHelper;
use App\ValueObjects\Api\Status;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repositories\UserRepository;

/**
 * @Route("/api/following")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class FollowingController extends AbstractController
{
    /**
     * @var \App\Repositories\UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/follow", name="following_follow", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function follow(Request $request)
    {
        $status = new Status();
        $content = HttpHelper::getContentAsArray($request);
        try{
            /**
             * @var User $currentUser
             */
            $currentUser = $this->getUser();
            $userToFollowId = $content->get('id');
            if(empty($userToFollowId)){
                throw new \Exception('Wrong Post');
            }
            /** @var User $userToFollow */
            $userToFollow = $this->userRepository->getByOneId($userToFollowId);
            if (!empty($userToFollow) && $userToFollow !== $currentUser) {
                $currentUser->follow($userToFollow);
                $this->userRepository->save($currentUser);
            } else {
                throw new \Exception('Wrong User');
            }
            $status->setSuccessStatus($userToFollow->getUsername());
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }

    /**
     * @Route("/unfollow", name="following_unfollow", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function unFollow(Request $request)
    {
        $status = new Status();
        $content = HttpHelper::getContentAsArray($request);
        try{
            /**
             * @var User $currentUser
             */
            $currentUser = $this->getUser();
            $userToUnFollowId = $content->get('id');
            if(empty($userToUnFollowId)){
                throw new \Exception('Wrong Post');
            }
            /** @var User $userToUnFollow */
            $userToUnFollow = $this->userRepository->getByOneId($userToUnFollowId);
            if (!empty($userToUnFollow) && $userToUnFollow !== $currentUser) {
                $currentUser->getFollowing()->removeElement($userToUnFollow);
                $this->userRepository->save($currentUser);
            } else {
                throw new \Exception('Wrong User');
            }
            $status->setSuccessStatus($userToUnFollow->getUsername());
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }
}
