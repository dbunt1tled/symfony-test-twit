<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeNotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class LikeNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MicroPost")
     */
    private $microPost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $likedBy;

    /**
     * @return null|MicroPost
     */
    public function getMicroPost()
    {
        return $this->microPost;
    }

    /**
     * @param MicroPost|null $microPost
     * @return LikeNotification
     */
    public function setMicroPost(?MicroPost $microPost): self
    {
        $this->microPost = $microPost;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getLikedBy(): ?User
    {
        return $this->likedBy;
    }

    /**
     * @param User|null $likedBy
     * @return LikeNotification
     */
    public function setLikedBy(?User $likedBy): self
    {
        $this->likedBy = $likedBy;
        return $this;
    }

}
