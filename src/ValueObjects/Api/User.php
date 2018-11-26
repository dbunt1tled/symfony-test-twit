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
    public $following;
    public $followers;
    /**
     * @var bool
     */
    private $asArray;

    /**
     * User constructor.
     * @param $user
     * @param bool $asArray
     */
    public function __construct($user, $asArray = false)
    {
        if (is_object($user)) {
            $this->setByObject($user);
        } elseif (is_array($user)) {
            $this->setByArray($user);
        }
        $this->asArray = $asArray;
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
        $this->following = [];

        $followings = $user->getFollowing();
        if(is_object($followings)) {
            foreach ($followings as $follow) {
                if (is_object($follow)) {
                    array_push($this->following,new User($follow->toArray()));
                }
            }
        }
        $this->followers = [];
        $followers = $user->getFollowers();
        if(is_object($followings)) {
            foreach ($followers as $follow) {
                if(is_object($follow)) {
                    array_push($this->followers,new User($follow->toArray()));
                }
            }
        }
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
        $this->lastName = $user['lastName'] ?? null;
        $this->firstName = $user['firstName'] ?? null;
        $this->email = $user['email'] ?? null;
        $this->username = $user['email'] ?? null;
        $this->following = [];

        if(!empty($user['followings'])) {
            foreach ($user['followings'] as $follow) {
                if(is_object($follow) && $this->asArray) {
                    array_push($this->following, null);
                } else {
                    array_push($this->following,new User($follow));
                }
            }
        }
        $this->followers = [];
        if(!empty($user['followers']) && $this->asArray) {
            foreach ($user['followers'] as $follow) {
                if(is_object($follow)) {
                    array_push($this->followers,null);
                } else {
                    array_push($this->followers,new User($follow));
                }
            }
        }
        $this->fullName =  trim($this->firstName . ' ' . $this->lastName);
        return $this;
    }
}