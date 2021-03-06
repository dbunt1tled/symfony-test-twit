<?php

namespace App\Controller;

use App\Document\Category;
use App\Document\Post;
use App\Document\User;
use App\Form\PostType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/category")
 */
class CategoryController extends AbstractController
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
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
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
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="category_index")
     */
    public function index()
    {
    }

    /**
     * @Route("/{slug}", name="category_post_list")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Category $category)
    {
        //$posts = $this->postRepository->findByCategory($category);
        $posts = $category->getPosts();
        return $this->render('category/show.html.twig',[
            'category' => $category,
            'posts' => $posts
        ]);
    }
}
