<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.10.18
 * Time: 11:10
 */

namespace App\Document\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait SluggableTrait
 *
 * @package App\Entity\Traits
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks
 */
trait SluggableTrait
{
    /**
     * @MongoDB\Field(type="string")
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     */
    private $slug;

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    /**
     * @param string|null $slug
     *
     * @return $this
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;
        return $this;
    }
}