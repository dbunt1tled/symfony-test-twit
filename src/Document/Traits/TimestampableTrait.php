<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.10.18
 * Time: 11:10
 */

namespace App\Document\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Trait TimestampableTrait
 *
 * @package App\Entity\Traits
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks
 */
trait TimestampableTrait
{
    /**
     * @MongoDB\Field(type="timestamp")
     */
    private $createdAt;
    /**
     * @MongoDB\Field(type="timestamp")
     */
    private $updatedAt;

    public function getCreatedAt(): ?\MongoTimestamp
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\MongoTimestamp $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    /**
     * Get updatedAt
     *
     * @return \MongoTimestamp
     */
    public function getUpdatedAt(): ?\MongoTimestamp
    {
        return $this->updatedAt;
    }
    /**
     * @param \MongoTimestamp $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\MongoTimestamp $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    /**
     * @MongoDB\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \MongoTimestamp());
        }
        if (!$this->getUpdatedAt()) {
            $this->setUpdatedAt(new \MongoTimestamp());
        }
    }
    /**
     * @MongoDB\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \MongoTimestamp());
    }
}