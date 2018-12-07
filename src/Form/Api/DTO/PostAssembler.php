<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.12.18
 * Time: 17:47
 */

namespace App\Form\Api\DTO;


use App\Document\Post;
use App\Form\Api\DTO\PostDTOInterface;
use App\Repositories\CategoryRepository;
use http\Exception\InvalidArgumentException;

class PostAssembler
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \App\Form\Api\DTO\PostDTOInterface $postDTO
     * @param Post|null $post
     * @return Post
     * @throws \MongoException
     */
    public function readDTO(PostDTOInterface $postDTO, ?Post $post = null): Post
    {
        if (!empty($postDTO->getErrors())) {
            throw new InvalidArgumentException('Wrong Post Data');
        }
        if (!$post) {
            $post = new Post();
        }
        $post->setTitle($postDTO->getTitle());
        $post->setSlug($postDTO->getSlug());
        $post->setText($postDTO->getText());
        $category = $this->categoryRepository->getByOneId($postDTO->getCategory());
        if($category) {
            $post->setCategory($category);
        }
        return $post;
    }

    /**
     * @param Post $post
     * @param \App\Form\Api\DTO\PostDTOInterface $postDTO
     * @return Post
     * @throws \MongoException
     */
    public function updatePost(Post $post, PostDTOInterface $postDTO): Post
    {
        return $this->readDTO($postDTO, $post);
    }

    /**
     * @param \App\Form\Api\DTO\PostDTOInterface $postDTO
     * @return Post
     * @throws \MongoException
     */
    public function createPost(PostDTOInterface $postDTO): Post
    {
        return $this->readDTO($postDTO);
    }
}