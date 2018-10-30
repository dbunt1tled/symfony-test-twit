<?php

namespace App\Controller;

use App\Document\Category;
use App\Document\Post;
use App\Document\User;
use App\Form\PostType;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Services\SearchService;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/search")
 */
class SearchController extends AbstractController
{

    /**
     * @var SearchService
     */
    private $searchService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        SearchService $searchService,
        TranslatorInterface $translator
    )
    {
        $this->searchService = $searchService;
        $this->translator = $translator;
    }

    /**
     * @param string $term
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/find/{term}", name="search_ajax")
     */
    public function ajax(string $term)
    {

        if(empty($term)) {
            return $this->json(['result'=> $this->translator->trans('search_empty')]);
        }
        $result = $this->searchService->findTermInBd($term);

        return $this->json(['result'=> '1']);
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
