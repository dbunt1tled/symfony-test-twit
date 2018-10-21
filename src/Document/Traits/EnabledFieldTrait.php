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
 * Trait EnabledFieldTrait
 *
 * @package App\Entity\Traits
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks
 */
trait EnabledFieldTrait
{
    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $enabled;

    public function __construct()
    {
        $this->enabled = false;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }
}