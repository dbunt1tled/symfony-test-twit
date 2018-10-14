<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.10.18
 * Time: 15:23
 */

namespace App\EventListener;


use App\Entity\UserPreferences;
use App\Events\UserRegisterEvent;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var string
     */
    private $defaultLanguage;

    public function __construct(Mailer $mailer, EntityManagerInterface $entityManager, string $defaultLanguage)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->defaultLanguage = $defaultLanguage;
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
        $preferences = new UserPreferences();
        $preferences->setLocale($this->defaultLanguage);
        $user->setPreferences($preferences);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->mailer->sendConfirmationEmail($user);

    }
}