<?php

namespace App\Controller\Api;

use App\Document\Post;
use App\Document\User;
use App\Form\PostType;
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
     * @var SessionInterface
     */
    private $session;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        postRepository $postRepository,
        UserRepository $userRepository,
        SessionInterface $session,
        AuthorizationCheckerInterface $authorizationChecker,
        FlashBagInterface $flashBag
    )
    {
        $this->postRepository = $postRepository;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
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
     * @Route("/add", name="api_post_add")
     * @param Request $request
     * @Security("is_granted('ROLE_USER')", message="Access Denied")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        /*if (!$this->authorizationChecker->isGranted('ROLE_USER')){
            throw new UnauthorizedHttpException();
        }/**/
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());
            $this->postRepository->save($post);
            return $this->redirectToRoute('post_index');
        }
        return $this->render('post/add.html.twig',[
            'form' => $form->createView(),
        ]);
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
     * @Route("/edit/{id}", name="api_post_edit")
     * @Security("is_granted('edit', post)", message="Access Denied")
     * @param Post $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Post $post, Request $request)
    {
        //$this->denyAccessUnlessGranted(PostVoter::EDIT,$post);
        /*
        if (!$this->authorizationChecker->isGranted(PostVoter::EDIT,$post)){
            throw new UnauthorizedHttpException();
        }
        /**/
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postRepository->save($post);
            return $this->redirectToRoute('post_post',['slug' => $post->getSlug()]);
        }
        return $this->render('post/edit.html.twig',[
            'form' => $form->createView(),
        ]);
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
}
