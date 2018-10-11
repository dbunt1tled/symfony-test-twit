<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"like" = "LikeNotification"})
 */
abstract class Notification
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $seen;

    public function __construct()
    {
        $this->seen = false;
        $this->user = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Collection
     */
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


}
