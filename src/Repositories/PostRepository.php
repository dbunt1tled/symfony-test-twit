<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.10.18
 * Time: 12:25
 */

namespace App\Repositories;


use App\Document\Post;
use App\Repositories\Traits\PagerTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;

class PostRepository extends DocumentRepository
{
    use PagerTrait;

    /**
     * UserRepository constructor.
     * @param DocumentManager $dm
     * @param UnitOfWork $uow
     * @param ClassMetadata $classMetadata
     */
    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $classMetadata)
    {
        parent::__construct($dm, $uow, $classMetadata);
    }

    /**
     * @param Post $post
     */
    public function save(Post $post)
    {
        $this->dm->persist($post);
        $this->dm->flush();
    }


    public function getPosts(int $page = 1, int $limit = 20, bool $asArray = false)
    {
        $offset = $this->getOffset($page, $limit);
        $qb = $this->dm->createQueryBuilder(Post::class);
        $qb->limit($limit)
            ->skip($offset);
        return $qb->getQuery()->execute();
    }
}