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
    /** @var \MongoClient  */
    private $db;
    private $dbName;
    private $connection;
    private $categoryCollection;

    private $childrenIndex = '__children';

    /**
     * CategoryRepository constructor.
     * @param DocumentManager $em
     * @param UnitOfWork $uow
     * @param ClassMetadata $class
     * @throws \Exception
     */
    public function __construct(DocumentManager $em, UnitOfWork $uow, ClassMetadata $class)
    {
        parent::__construct($em, $uow, $class);
        $this->dbName = getenv('MONGODB_DB');
        $connection = $em->getConnection();

        $this->connection = $connection->getMongoClient();
        $this->db = $this->connection->selectDB($this->dbName);
        $this->categoryCollection = $this->db->selectCollection('Category');
    }
    /**
     * @param Category $category
     */
    public function save(Category $category)
    {
        $this->dm->persist($category);
        $this->dm->flush();
    }

    /**
     * @param $id
     * @return array|object|null
     * @throws \MongoException
     */
    public function getByOneId($id)
    {
        if (!($id instanceof \MongoId)) {
            $id = new \MongoId((string)$id);
        }
        return $this->createQueryBuilder()
            ->field('id')->equals($id)
            ->getQuery()
            ->getSingleResult();
    }

    public function getFullTree()
    {
        return $this->createQueryBuilder()
            ->sort('path')
            ->getQuery()
            ->execute();
    }
    public function getFullTreeRaw()
    {
        return $this->categoryCollection->find([/*'enabled'=>true/**/])->sort(['path' => 1]);
        /*
        $query = [
            'aggregate' => 'Category',
            'pipeline' => [
                //[ '$match' => ['enabled'=> true]],
                [ '$sort' => ['path' => 1]]
            ],
            'cursor' => [],
        ];
        return $this->executeJsAll($query);/**/
    }

    public function getFullTreeArray()
    {
        $nestedTree = array();
        $tree = $this->getFullTreeRaw();
        if($tree) {
            $stack = array();
            foreach ($tree as $id => $node) {
                $item = (array)$node;
                $item['_id'] = $item['id'] = (string)$item['_id'];
                $item[$this->childrenIndex] = [];
                $l = count($stack);
                while ($l > 0 && $stack[$l - 1]['level'] >= $item['level']) {
                    array_pop($stack);
                    $l--;
                }
                if ($l == 0) {
                    // Assigning the root child
                    $i = count($nestedTree);
                    $nestedTree[$i] = $item;
                    $stack[] = &$nestedTree[$i];
                } else {
                    // Add child to parent
                    $i = count($stack[$l - 1][$this->childrenIndex]);
                    $stack[$l - 1][$this->childrenIndex][$i] = $item;
                    $stack[] = &$stack[$l - 1][$this->childrenIndex][$i];
                }
            }
        }
        /**/
        return $nestedTree;
    }
    /**
     * @param $query
     * @return array|\MongoCommandCursor
     */
    public function executeJsAll($query)
    {
        $cursor = new \MongoCommandCursor($this->connection, $this->dbName, $query);
        $cursor->setReadPreference($this->connection->getReadPreference());
        return iterator_to_array($cursor);
    }
}