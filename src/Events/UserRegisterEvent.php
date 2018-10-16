<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.10.18
 * Time: 15:17
 */

namespace App\Events;


use App\Document\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    const NAME = 'user.register';
    /**
     * @var User
     */
    private $registerUser;

    public function __construct(User $registerUser)
    {

        $this->registerUser = $registerUser;
    }

    /**
     * @return User
     */
    public function getRegisterUser(): User
    {
        return $this->registerUser;
    }

}