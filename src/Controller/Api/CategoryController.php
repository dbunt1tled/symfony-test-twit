<?php

namespace App\Controller\Api;

use App\Document\Post;
use App\Document\User;
use App\Form\PostType;
use App\Repositories\CategoryRepository;
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
 * Class CategoryController
 * @package App\Controller\Api
 * @Route("/api/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        UserRepository $userRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        CategoryRepository $categoryRepository
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/tree-all", name="api_category_tree_all")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoriesTreeAll()
    {
        $tree = $this->categoryRepository->getFullTreeArray();
        return $this->json($tree, Response::HTTP_OK);
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
