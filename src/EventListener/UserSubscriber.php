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
use App\Repositories\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $defaultLanguage;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Mailer $mailer, UserRepository $userRepository, string $defaultLanguage)
    {
        $this->mailer = $mailer;
        $this->defaultLanguage = $defaultLanguage;
        $this->userRepository = $userRepository;
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
        $currentPreferences = $user->getPreferences();
        $currentPreferences->setLocale($this->defaultLanguage);
        $user->setPreferences($currentPreferences);
        $this->userRepository->save($user);
        return $this->mailer->sendConfirmationEmail($user);

    }
}