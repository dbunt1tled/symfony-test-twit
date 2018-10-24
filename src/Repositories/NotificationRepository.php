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
     * @param Notification $notification
     */
    public function save(Notification $notification)
    {
        $this->dm->persist($notification);
        $this->dm->flush();
    }

    public function findUnSeenByUser(User $user)
    {
        $qb = $this->dm->createQueryBuilder(Notification::class);
        return $qb->field('user')->equals($user)
            ->field('seen')->equals(false)
            ->getQuery()
            ->count();
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function markAllAsReadByUser(User $user)
    {
        $qb = $this->dm->createQueryBuilder(Notification::class);
        return $qb->findAndUpdate()
            ->field('user')->equals($user)
            ->field('seen')->set(true)
            ->getQuery()
            ->execute();
    }
}