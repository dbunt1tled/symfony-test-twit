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

    /** @var \MongoClient  */
    private $db;
    private $dbName;
    private $connection;
    /**
     * UserRepository constructor.
     * @param DocumentManager $dm
     * @param UnitOfWork $uow
     * @param ClassMetadata $classMetadata
     */
    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $classMetadata)
    {
        parent::__construct($dm, $uow, $classMetadata);

        $this->dbName = getenv('MONGODB_DB');
        $connection = $dm->getConnection();

        $this->connection = $connection->getMongo();
        $this->db = $this->connection->selectDB($this->dbName);
    }

    /**
     * @param Post $post
     */
    public function save(Post $post)
    {
        $this->dm->persist($post);
        $this->dm->flush();
    }

    /**
     * @param string $field
     * @param string $data
     *
     * @return array|null|object
     */
    public function findOneByProperty($field, $data)
    {
        return
            $this->dm->createQueryBuilder(Post::class)
                ->field($field)->equals($data)
                ->getQuery()
                ->getSingleResult();
    }

    public function getPosts(int $page = 1, int $limit = 20, bool $asArray = false)
    {
        $offset = $this->getOffset($page, $limit);
        $qb = $this->dm->createQueryBuilder(Post::class);
        $qb->limit($limit)
            ->skip($offset);
        return $qb->getQuery()->execute();
    }
    public function getPostsA(int $page = 1, int $limit = 20, bool $asArray = false)
    {
        $offset = $this->getOffset($page, $limit);
        $qb = $this->dm->getDocumentCollection(Post::class)->createAggregationBuilder();
        $qb->match()
                ->field('user.$id')
                ->equals(new \MongoId("5bcae27f591a302433165efc"))
        ;
        return iterator_to_array($qb->execute(['cursor' => []]));
    }
    public function getPostsWithUsers(int $page = 1, int $limit = 20, bool $asArray = false)
    {
        $offset = $this->getOffset($page, $limit);
        $query = [
            'aggregate' => 'Post',
            'pipeline' => [
                //[ '$match' => ['user.$id'=> new \MongoId("5bc8bf55591a3027e22d9d72")]],
                [ '$match' => ['enabled'=> true]],
                [ '$project' => ['text' => 1,'title' => 1, 'slug' => 1, 'createdAt' => 1, 'updatedAt' => 1,
                    'userobj' => [
                        '$arrayToObject' => [
                            '$map' => [
                                'input' => ['$objectToArray'=> '$user'],
                                'in' => [
                                    'k' => [
                                        '$cond' => [
                                            ['$eq' => [['$substrCP' => ['$$this.k', 0, 1]], ['$literal' => '$']]],
                                            ['$substrCP' => ['$$this.k', 1, ['$strLenCP' => '$$this.k']]],
                                            '$$this.k'
                                        ]
                                    ],
                                    'v' => '$$this.v'
                                ],
                            ],
                        ],
                    ],
                ]],
                [ '$lookup' => ['from' => 'User','localField' => 'userobj.id','foreignField' => '_id','as' => 'user']],
                [ '$project' => ['text' => 1,'title' => 1, 'slug' => 1, 'createdAt' => 1, 'updatedAt' => 1,
                    'user' => [
                        '$arrayElemAt' => ['$user',0],
                    ],
                ]],
                ['$addFields' => ['user.fullName' => [
                     '$concat' => [ '$user.firstName', ' ', '$user.lastName' ]
                    ]
                ]],
                ['$addFields' => ['user.username' => '$user.email']],
                ['$addFields' => ['id' => '$_id']],
                ['$skip' => $offset],
                ['$limit' => $limit],
            ],
            'cursor' => [],/**/
        ];
        return $this->executeJsAll($query);
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
    public function executeJsFirst(array $query)
    {
        $results = $this->db->command($query);
        return $results;
    }
}