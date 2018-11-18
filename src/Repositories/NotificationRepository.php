<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.10.18
 * Time: 12:25
 */

namespace App\Repositories;


use App\Document\Notification;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;

class NotificationRepository extends DocumentRepository
{
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

        $this->connection = $connection->getMongoClient();
        $this->db = $this->connection->selectDB($this->dbName);
    }

    /**
     * @param Notification $notification
     */
    public function save(Notification $notification)
    {
        $this->dm->persist($notification);
        $this->dm->flush();
    }

    public function findCountUnSeenByUser(User $user)
    {
        $qb = $this->dm->createQueryBuilder(Notification::class);
        return $qb->field('user')->equals($user)
            ->field('seen')->equals(false)
            ->getQuery()
            ->count();
    }

    public function findUnSeenByUser(User $user)
    {
        $qb = $this->dm->createQueryBuilder(Notification::class);
        return $qb->field('user')->equals($user)
            ->field('seen')->equals(false)
            ->getQuery()
            ->execute();
    }

    public function findById($id)
    {
        if(! ($id instanceof \MongoId) ) {
            $id = new \MongoId($id);
        }
        $qb = $this->dm->createQueryBuilder(Notification::class);
        return $qb->field('id')->equals($id)
            ->getQuery()
            ->getSingleResult();
    }
    public function findUnSeenLikesByUser(User $user)
    {
        $query = [
            'aggregate' => 'Notification',
            'pipeline' => [
                ['$match' => [
                    '$and' => [
                        ['type' => 'like'],
                        ['seen' => false],
                        ['user.$id' => new \MongoId($user->getId())]
                    ],
                ]],
                ['$project' => ['_id' => 1, 'seen' => 1, 'type' => 1,
                    'likedByObj' => $this->mongoArrayToObject('likedBy'),
                    'postObj' => $this->mongoArrayToObject('post'),
                    'userObj' => $this->mongoArrayToObject('user'),
                ]],
                    ['$lookup' => ['from' => 'User', 'localField' => 'likedByObj.id', 'foreignField' => '_id', 'as' => 'likedBy']],
                    ['$lookup' => ['from' => 'Post', 'localField' => 'postObj.id', 'foreignField' => '_id', 'as' => 'post']],
                    ['$project' => ['_id' => 1, 'seen' => 1, 'type' => 1,
                        'likedBy' => [
                            '$arrayElemAt' => ['$likedBy', 0],
                        ],
                        'post' => [
                            '$arrayElemAt' => ['$post', 0],
                        ],
                        'user' => [
                            '$arrayElemAt' => ['$user', 0],
                        ],
                    ]],
                    ['$addFields' => ['id' => '$_id']],
                ],
            'cursor' => [],
        ];
        return $this->executeJsAll($query);
    }
    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function markAllAsReadByUser(User $user)
    {
        $qb = $this->dm->createQueryBuilder(Notification::class);
        return $qb->updateMany()
            ->field('user')->equals($user)
            ->field('seen')->set(true)
            ->getQuery()
            ->execute();
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

    private function mongoArrayToObject(string $objName):array
    {
        return [
            '$arrayToObject' => [
                '$map' => [
                    'input' => ['$objectToArray' => '$'.$objName],
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
            ]];
    }
}