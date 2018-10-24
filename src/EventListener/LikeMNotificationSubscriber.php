<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.10.18
 * Time: 15:16
 */

namespace App\EventListener;


use App\Document\LikeNotification;
use App\Document\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\PersistentCollection;


class LikeMNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }
    public function onFlush(OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();
        /** @var PersistentCollection $collectionUpdates */
        $collectionUpdates = $uow->getScheduledCollectionUpdates();
        foreach ($collectionUpdates as $collectionUpdate) {
            $post = $collectionUpdate->getOwner();

            if (!$post instanceof Post) {
                continue;
            }

            if('likedBy' !== $collectionUpdate->getMapping()['fieldName']) {
                continue;
            }

            $insertDiff = $collectionUpdate->getInsertDiff();

            if(!count($insertDiff)) {
                return;
            }

            $notification = new LikeNotification();
            $notification->setUser($post->getUser())
                ->setPost($post)
                ->setLikedBy(reset($insertDiff));
            $dm->persist($notification);
            $uow->computeChangeSet($dm->getClassMetadata(LikeNotification::class),$notification);
            //$dm->flush();
        }
    }

}