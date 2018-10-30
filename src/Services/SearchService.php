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

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        SessionInterface $session,
        FlashBagInterface $flashBag
    )
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->session = $session;
        $this->flashBag = $flashBag;
    }
    public function findTermInBd(string $term)
    {
        $result = $this->postRepository->findInBd($term);
        if(!$result){
            return false;
        }
        dump($result);
        die;
        foreach ($result as $value) {
            dump($value);
        }
        die;
    }
}