<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.18
 * Time: 12:42
 */

namespace App\ValueObjects\Api;


class User
{
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $username;
    public $fullName;
    /**
     * Post constructor.
     * @param \App\Document\User|array|null $user
     */
    public function __construct($user)
    {
        if (is_object($user)) {
            $this->setByObject($user);
        } elseif (is_array($user)) {
            $this->setByArray($user);
        }

    }

    /**
     * @param \App\Document\User $user
     * @return $this
     */
    public function setByObject(\App\Document\User $user)
    {
        $this->id = $user->getId();
        $this->lastName = $user->getLastName();
        $this->firstName = $user->getFirstName();
        $this->username = $user->getUsername();
        $this->email = $user->getEmail();
        $this->fullName =  trim($this->firstName . ' ' . $this->lastName);
        return $this;
    }
    /**
     * @param array $user
     * @return $this
     */
    public function setByArray(array $user)
    {
        if(isset($user['id'])){
            $this->id = (string)$user['id'];
        }elseif (isset($user['_id'])) {
            $this->id = (string)$user['_id'];
        }
        $this->lastName = $user['lastName']??$user['lastName'];
        $this->firstName = $user['firstName']??$user['firstName'];
        $this->email = $user['email']??$user['email'];
        $this->username = $user['email']??$user['email'];
        $this->fullName =  trim($this->firstName . ' ' . $this->lastName);
        return $this;
    }
}