<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MicroPostController extends AbstractController
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
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        MicroPostRepository $microPostRepository,
        UserRepository $userRepository,
        SessionInterface $session,
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
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="micro_post_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $currentUser = $this->getUser();
        $usersToFollow = [];

        if($currentUser instanceof User) {
            $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());
            $usersToFollow = count($posts) === 0 ? $this->userRepository->findAllWithMoreThan4PostsExceptUser($currentUser) : [];
        } else {
            $posts = $this->microPostRepository->findBy([],['created_at'=>'DESC']);
        }

        return $this->render('micro-post/index.html.twig',[
            'posts' => $posts,
            'usersToFollow' => $usersToFollow,
        ]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     * @param User $userWithPosts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userPosts(User $userWithPosts)
    {
        //$posts = $this->microPostRepository->findBy(['user' => $userWithPosts],['created_at'=>'DESC']);
        $posts = $userWithPosts->getPosts(); //This method not sorted

        return $this->render('micro-post/user-posts.html.twig',[
            'posts' => $posts,
            'user' => $userWithPosts,
        ]);
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @param Request $request
     * @Security("is_granted('ROLE_USER')", message="Access Denied")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        /*if (!$this->authorizationChecker->isGranted('ROLE_USER')){
            throw new UnauthorizedHttpException();
        }/**/
        $microPost = new MicroPost();
        $form = $this->createForm(MicroPostType::class,$microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost->setUser($this->getUser());
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();
            return $this->redirectToRoute('micro_post_index');
        }
        return $this->render('micro-post/add.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('delete', post)", message="Access Denied")
     * @param MicroPost $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(MicroPost $post)
    {
        //$this->denyAccessUnlessGranted(MicroPostVoter::DELETE,$post);
        /*
        if (!$this->authorizationChecker->isGranted(MicroPostVoter::DELETE,$post)){
            throw new UnauthorizedHttpException();
        }
        /**/
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->flashBag->add('info', 'Micro Post was Deleted');
        return $this->redirectToRoute('micro_post_index');
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', post)", message="Access Denied")
     * @param MicroPost $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(MicroPost $post, Request $request)
    {
        //$this->denyAccessUnlessGranted(MicroPostVoter::EDIT,$post);
        /*
        if (!$this->authorizationChecker->isGranted(MicroPostVoter::EDIT,$post)){
            throw new UnauthorizedHttpException();
        }
        /**/
        $form = $this->createForm(MicroPostType::class,$post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            return $this->redirectToRoute('micro_post_post',['id' => $post->getId()]);
        }
        return $this->render('micro-post/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     * @param $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(MicroPost $post)
    {
        // $post = $this->microPostRepository->find($id);
        return $this->render('micro-post/show.html.twig',[
            'post' => $post
        ]);
    }
}
