<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 30.10.18
 * Time: 12:15
 */

namespace App\Services;


use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchService
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
     * @var SessionInterface
     */
    private $session;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    )
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->session = $session;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }
    public function findTermInBd(string $term)
    {
        $result = $this->postRepository->findInBd($term);
        if(!$result){
            return false;
        }
        $res = [];
        foreach ($result as $key => $value) {
            switch($key) {
                case 'searchUser':
                        foreach ($value as $user) {
                            $res[] = [
                                'text' => $user['firstName'].' '.$user['lastName'],
                                'link' => $this->urlGenerator->generate('post_user',['email'=> $user['email']])
                            ];
                        }
                    break;
                case 'searchPost':
                    foreach ($value as $post) {
                        $res[] = [
                            'text' => $post['title'],
                            'link' => $this->urlGenerator->generate('post_post',['slug'=> $post['slug']])
                        ];
                    }
                    break;
                case 'searchCategory':
                    foreach ($value as $category) {
                        $res[] = [
                            'text' => $category['title'],
                            'link' => $this->urlGenerator->generate('category_post_list',['slug'=> $category['slug']])
                        ];
                    }
                    break;
            }
        }
        return $res;
    }
}