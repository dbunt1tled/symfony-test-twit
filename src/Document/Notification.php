<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\NotificationRepository")
 * @MongoDB\InheritanceType("SINGLE_COLLECTION")
 * @MongoDB\DiscriminatorField(name="type")
 * @MongoDB\DiscriminatorMap({"like" = "LikeNotification"})
 */
abstract class Notification
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    private $user;

    /**
     * @MongoDB\Field(type="boolean")
     */
    private $seen;

    public function __construct()
    {
        $this->seen = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * @param User|null $user
     * @return Notification
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param boolean $seen
     * @return Notification
     */
    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;
        return $this;
    }
    public function toArray() {
        return get_object_vars($this);
        // alternatively, you could do:
        // return ['username' => $this->username, 'password' => '****']
    }

}
