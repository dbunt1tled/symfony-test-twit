<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 10.11.18
 * Time: 17:48
 */

namespace App\EventListener\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getData();

        // Add IP User
        $payload['ip'] = $request->getClientIp();

        //Override token expiration date calculation to be more flexible
        $expiration = new \DateTime('+1 day');
        $expiration->setTime(2, 0, 0);
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }

    /**
     * @param JWTDecodedEvent $event
     *
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getPayload();

        if (!isset($payload['ip']) || $payload['ip'] !== $request->getClientIp()) {
            $event->markAsInvalid();
        }
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // $data ['roles'] = $user->getRoles();
        $data ['username'] = $user->getUsername();
        $event->setData($data);
    }
}