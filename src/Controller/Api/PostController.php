<?php

namespace App\Controller\Api;

use App\Document\Post;
use App\Document\User;
use App\Form\Api\DTO\PostAssembler;
use App\Form\Api\Http\Requests\Posts\AddPostRequest;
use App\Form\Api\Http\Requests\Posts\ManagePostsRequest;
use App\Form\PostType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\ValueObjects\Api\Status;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/api/posts")
 */
class PostController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var PostAssembler
     */
    private $postAssembler;

    public function __construct(
        postRepository $postRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        PostAssembler $postAssembler
    )
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->postAssembler = $postAssembler;
    }

    /**
     * @Route("/", name="api_posts")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $currentUser = $this->getUser();
        $usersToFollow = [];
        $page = (int)$request->get('page',1);
        $limit = (int)$request->get('limit',20);
        if($currentUser instanceof User) {
            /*$posts = $this->postRepository->findAllByUsers($currentUser->getFollowing());
            $usersToFollow = count($posts) === 0 ? $this->userRepository->findAllWithMoreThan4PostsExceptUser($currentUser) : [];
            /**/
            $posts = $this->postRepository->getPostsWithUsers($page,$limit,true);
        } else {
            //$posts = $this->postRepository->findBy([],['created_at'=>'DESC']);
            //$posts = $this->postRepository->getPosts(1,20,true);
            $posts = $this->postRepository->getPostsWithUsers($page,$limit,true);
        }
        $posts1 = array_map(function ($val){
            return new \App\ValueObjects\Api\Post($val);
        },$posts);

        return $this->json($posts1,Response::HTTP_OK);
    }

    /**
     * @Route("/add", name="api_post_add")
     * @Method({"POST"})
     * @param AddPostRequest $request
     * @Security("is_granted('ROLE_USER')", message="Access Denied")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add(AddPostRequest $request)
    {
        /*if (!$this->authorizationChecker->isGranted('ROLE_USER')){
            throw new UnauthorizedHttpException();
        }/**/
        $status = new Status();
        try {
            $errors = $request->getErrors();
            if (!empty($errors)) {
                $field = key($errors);
                $firstError = $errors[$field][0];
                throw new BadRequestHttpException($field  .'::' . $firstError);
            }
            $post = $this->postAssembler->createPost($request);
            $post->setUser($this->getUser());
            $this->postRepository->save($post);

            $status->setSuccessStatus($post->getSlug());

        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{email}", name="api_post_user")
     * @param User $userWithPosts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userPosts(User $userWithPosts)
    {
        $posts = $this->postRepository->findBy(['user' => $userWithPosts],['created_at'=>'DESC']);

        //$posts = iterator_to_array($userWithPosts->getPosts()); //This method not sorted

        $posts1 = array_map(function ($val){
            return new \App\ValueObjects\Api\Post($val,true);
        },$posts);
        /**/
        $user = new \App\ValueObjects\Api\User($userWithPosts,true);
        return $this->json([
            'posts' => $posts1,
            'user' => $user,
        ],Response::HTTP_OK);
    }
    /**
     * @Route("/delete/{id}", name="api_post_delete")
     * @Security("is_granted('delete', post)", message="Access Denied")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Post $post)
    {
        //$this->denyAccessUnlessGranted(PostVoter::DELETE,$post);
        /*
        if (!$this->authorizationChecker->isGranted(PostVoter::DELETE,$post)){
            throw new UnauthorizedHttpException();
        }
        /**/
        $this->postRepository->remove($post);
        $this->flashBag->add('info', 'Post was Deleted');
        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/{slug}", name="api_post_post")
     * @Method({"GET"})
     * @param $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(Post $post)
    {
        // $post = $this->postRepository->find($id);
        $postVO = new \App\ValueObjects\Api\Post($post);
        header('Content-Type: cli');
        return $this->json($postVO, Response::HTTP_OK);
    }

    /**
     * @Route("/{slug}/update", name="api_post_update")
     * @Security("is_granted('edit', post)", message="Access Denied")
     * @param Post $post
     * @param ManagePostsRequest $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Post $post, ManagePostsRequest $request)
    {
        $status = new Status();
        try {
            $errors = $request->getErrors();
            if (!empty($errors)) {
                $field = key($errors);
                $firstError = $errors[$field][0];
                throw new BadRequestHttpException($field . '::' . $firstError);
            }
            $this->postAssembler->updatePost($post, $request);
            $this->postRepository->save($post);
            $status->setSuccessStatus();
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }

    /**
     * @Route("/{slug}/manage", name="api_post_manage")
     * @Method({"GET"})
     * @param $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manage(Post $post)
    {
        // $post = $this->postRepository->find($id);
        $postVO = new \App\ValueObjects\Api\Post($post,true);
        $categories = $this->categoryRepository->getFullTree();
        $categoriesResult = [];
        if($categories) {
            foreach ($categories as $category) {
                $categoriesResult[] = new \App\ValueObjects\Api\Short\Category($category);
            }
        }
        header('Content-Type: cli');
        return $this->json([
            'post' => $postVO,
            'categories' => $categoriesResult,
        ], Response::HTTP_OK);
    }
}
