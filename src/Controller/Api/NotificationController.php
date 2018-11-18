<?php

namespace App\Controller\Api;

use App\Document\User;
use App\Document\Notification;
use App\Repositories\NotificationRepository;
use App\ValueObjects\Api\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/notification")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class NotificationController extends AbstractController
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(
        NotificationRepository $notificationRepository
    )
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="api_notification_unread")
     */
    public function unreadCount()
    {
        /** @var User $user */
        $user = $this->getUser();
        $count = (int) $this->notificationRepository->findCountUnSeenByUser($user);
        return $this->json([
            'count' => $count
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/all", name="api_notification_all")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function notification()
    {
        /** @var User $user */
        $user = $this->getUser();

        $notifications = $this->notificationRepository->findUnSeenLikesByUser($user);
        $notifications = array_map(function ($val){
            return new \App\ValueObjects\Api\Notification($val);
        },$notifications);
        return $this->json([
            'notifications' => $notifications
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/acknowledge", name="api_notification_acknowledge")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function acknowledge(Request $request)
    {
        $status = new Status();
        $content = $this->getContentAsArray($request);
        try{
            $notificationId = $content->get('id');
            if(empty($notificationId)){
                throw new \Exception('Wrong Notification');
            }
            $notification = $this->notificationRepository->findById($notificationId);
            if(empty($notification)){
                throw new \Exception('Notification not found');
            }
            $notification->setSeen(true);
            $this->notificationRepository->save($notification);
            $status->setSuccessStatus();
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }

    /**
     * @Route("/acknowledge-all", name="api_notification_acknowledge_all")
     * @Method({"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function acknowledgeAll()
    {
        $status = new Status();
        try{
            /** @var User $user */
            $user = $this->getUser();
            $this->notificationRepository->markAllAsReadByUser($user);
            $status->setSuccessStatus();
        }catch (\Exception $e) {
            $status->setFailureStatus($e->getMessage());
        }
        return $this->json($status, Response::HTTP_OK);
    }

    protected function getContentAsArray(Request $request){
        $content = $request->getContent();

        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        /*if(!Validator::isValidJsonString($content)){
            throw new BadRequestHttpException("Content is not a valid json");
        }/**/

        return new ArrayCollection(json_decode($content, true));
    }
}
