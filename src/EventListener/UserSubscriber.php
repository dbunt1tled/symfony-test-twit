<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.10.18
 * Time: 15:23
 */

namespace App\EventListener;


use App\Events\UserRegisterEvent;
use App\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister',
        ];
    }
    public function onUserRegister(UserRegisterEvent $event)
    {
        $user = $event->getRegisterUser();
        return $this->mailer->sendConfirmationEmail($user);

    }
}