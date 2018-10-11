<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
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

    public function __construct(
        NotificationRepository $notificationRepository,
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
        return $this->redirectToRoute('notification_all')
    }

    /**
     * @Route("/acknowledge-all", name="notification_acknowledge_all")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acknowledgeAll()
    {
        $notification->setSeen(true);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        return $this->redirectToRoute('notification_all')
    }


}
