<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Document\User as MUser;
use App\Document\Notification as MNotification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/notification")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class NotificationController extends AbstractController
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var \App\Repositories\NotificationRepository
     */
    private $notificationMRepository;

    public function __construct(
        NotificationRepository $notificationRepository,
        \App\Repositories\NotificationRepository $notificationMRepository,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        AuthorizationCheckerInterface $authorizationChecker,
        FlashBagInterface $flashBag
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->flashBag = $flashBag;
        $this->notificationMRepository = $notificationMRepository;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount()
    {
        /** @var User $user */
        $user = $this->getUser();
        $count = $this->notificationRepository->findUnSeenByUser($user);
        return $this->json([
            'count' => $count
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/all", name="notification_all")
     */
    public function notification()
    {
        /** @var User $user */
        $user = $this->getUser();
        $notifications = $this->notificationRepository->findBy(['seen'=>false,'user'=>$user]);
        return $this->render('notification/notification.html.twig',[
            'notifications' => $notifications,
        ]);
    }

    /**
     * @Route("/acknowledge/{id}", name="notification_acknowledge")
     * @param Notification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acknowledge(Notification $notification)
    {
        $notification->setSeen(true);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        return $this->redirectToRoute('notification_all');
    }

    /**
     * @Route("/acknowledge-all", name="notification_acknowledge_all")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acknowledgeAll()
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->notificationRepository->markAllAsReadByUser($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('notification_all');
    }

    /**
     * @Route("/m-all", name="notification_m_all")
     */
    public function mNotification()
    {
        /** @var MUser $user */
        $user = $this->getUser();
        $notifications = $this->notificationMRepository->findUnSeenByUser($user);
        return $this->render('notification/m-notification.html.twig',[
            'notifications' => $notifications,
        ]);
    }
    /**
     * @Route("/m-unread-count", name="notification_m_unread")
     */
    public function mUnreadCount()
    {
        /** @var MUser $user */
        $user = $this->getUser();
        $count = (int) $this->notificationMRepository->findCountUnSeenByUser($user);
        return $this->json([
            'count' => $count
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/m-acknowledge/{id}", name="notification_m_acknowledge")
     * @param MNotification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function mAcknowledge(MNotification $notification)
    {
        $notification->setSeen(true);
        $this->notificationMRepository->save($notification);
        return $this->redirectToRoute('notification_m_all');
    }

    /**
     * @Route("/m-acknowledge-all", name="notification_m_acknowledge_all")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function mAcknowledgeAll()
    {
        /** @var MUser $user */
        $user = $this->getUser();
        $this->notificationMRepository->markAllAsReadByUser($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('notification_m_all');
    }

}
