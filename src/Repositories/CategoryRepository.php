<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.10.18
 * Time: 12:25
 */

namespace App\Repositories;


use App\Document\Category;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Gedmo\Tree\Document\MongoDB\Repository\MaterializedPathRepository;

class CategoryRepository extends MaterializedPathRepository
{
    public function __construct(DocumentManager $em, UnitOfWork $uow, ClassMetadata $class)
    {
        parent::__construct($em, $uow, $class);
    }
    /**
     * @param Category $category
     */
    public function save(Category $category)
    {
        $this->dm->persist($category);
        $this->dm->flush();
    }

    public function getByOneId($id)
    {
        return $this->createQueryBuilder()
            ->field('id')->equals($id)
            ->getQuery()
            ->getSingleResult();
    }

}