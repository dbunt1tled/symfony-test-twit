<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\LikeNotificationRepository")
 * @MongoDB\HasLifecycleCallbacks()
 */
class LikeNotification extends Notification
{
    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\Post")
     */
    private $post;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    private $likedBy;

    /**
     * @return null|Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post|null $post
     * @return LikeNotification
     */
    public function setPost(?Post $post): self
    {
        $this->post = $post;
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
    public function toArray() {
        return get_object_vars($this);
        // alternatively, you could do:
        // return ['username' => $this->username, 'password' => '****']
    }
}
