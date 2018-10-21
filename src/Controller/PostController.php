<?php

namespace App\Controller;

use App\Document\Post;
use App\Document\User;
use App\Form\PostType;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @Route("/", name="post_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $currentUser = $this->getUser();
        $usersToFollow = [];
        if($currentUser instanceof User) {
            $posts = $this->postRepository->findAllByUsers($currentUser->getFollowing());
            $usersToFollow = count($posts) === 0 ? $this->userRepository->findAllWithMoreThan4PostsExceptUser($currentUser) : [];
        } else {
            //$posts = $this->postRepository->findBy([],['created_at'=>'DESC']);
            //$posts = $this->postRepository->getPosts(1,20,true);
            $posts = $this->postRepository->getPostsWithUsers(1,20,true);
        }

        return $this->render('post/index.html.twig',[
            'posts' => $posts,
            'usersToFollow' => $usersToFollow,
        ]);
    }

    /**
     * @Route("/user/{username}", name="post_user")
     * @param User $userWithPosts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userPosts(User $userWithPosts)
    {
        //$posts = $this->postRepository->findBy(['user' => $userWithPosts],['created_at'=>'DESC']);
        $posts = $userWithPosts->getPosts(); //This method not sorted

        return $this->render('post/user-posts.html.twig',[
            'posts' => $posts,
            'user' => $userWithPosts,
        ]);
    }

    /**
     * @Route("/add", name="post_add")
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
     * @Route("/delete/{id}", name="post_delete")
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
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->flashBag->add('info', 'Micro Post was Deleted');
        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/edit/{id}", name="post_edit")
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
            return $this->redirectToRoute('post_post',['id' => $post->getId()]);
        }
        return $this->render('post/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_post")
     * @param $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(Post $post)
    {
        // $post = $this->postRepository->find($id);
        return $this->render('post/show.html.twig',[
            'post' => $post
        ]);
    }
}
