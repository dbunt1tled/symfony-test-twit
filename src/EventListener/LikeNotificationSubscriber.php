<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.10.18
 * Time: 15:16
 */

namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        /** @var PersistentCollection $collectionUpdates */
        $collectionUpdates = $uow->getScheduledCollectionUpdates();
        foreach ($collectionUpdates as $collectionUpdate) {
            $microPost = $collectionUpdate->getOwner();

            if (!$microPost instanceof MicroPost) {
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
            $notification->setUser($microPost->getUser())
                ->setMicroPost($microPost)
                ->setLikedBy(reset($insertDiff));
            $em->persist($notification);

            $uow->computeChangeSet($em->getClassMetadata(LikeNotification::class),$notification);
            //$em->flush();
        }
    }

}