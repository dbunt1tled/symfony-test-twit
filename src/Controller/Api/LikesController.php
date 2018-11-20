<?php

namespace App\Controller\Api;

use App\Document\Post;
use App\Document\User;
use App\Helpers\HttpHelper;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\ValueObjects\Api\Status;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/likes")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class LikesController extends AbstractController
{

    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository
    )
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * @Route("/like", name="api_likes_like", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function like(Request $request)
    {
        $status = new Status();
        $content = HttpHelper::getContentAsArray($request);

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        try{
            $postId = $content->get('id');
            if(empty($postId)){
                throw new \Exception('Wrong Post');
            }
            $post = $this->postRepository->findOneById($postId);
            if(empty($post)){
                throw new \Exception('Post not found');
            }
            $post->like($currentUser);
            $this->postRepository->save($post);
            $status->setSuccessStatus($post->getLikedBy()->count());
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }
    /**
     * @Route("/unlike", name="api_likes_unlike", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function unLike(Request $request)
    {
        $status = new Status();
        $content = HttpHelper::getContentAsArray($request);

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        try{
            $postId = $content->get('id');
            if(empty($postId)){
                throw new \Exception('Wrong Post');
            }
            $post = $this->postRepository->findOneById($postId);
            if(empty($post)){
                throw new \Exception('Post not found');
            }
            $post->unLike($currentUser);
            $this->postRepository->save($post);
            $status->setSuccessStatus($post->getLikedBy()->count());
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }


}
