<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 18.10.18
 * Time: 10:41
 */

namespace App\Document;


use App\Document\Traits\EnabledFieldTrait;
use App\Document\Traits\SluggableTrait;
use App\Document\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\PostRepository")
 * @MongoDB\HasLifecycleCallbacks
 */
class Post
{
    use TimestampableTrait, SluggableTrait, EnabledFieldTrait;

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min="10", minMessage="minimum 10 symbols")
     */
    private $text;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min="10", minMessage="minimum 10 symbols")
     */
    private $title;

    /** @MongoDB\ReferenceOne(targetDocument="User", inversedBy="posts") */
    private $user;

    /** @MongoDB\ReferenceMany(targetDocument="User", inversedBy="postsLiked") */
    private $likedBy;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->likedBy = new ArrayCollection();
    }
    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $text
     * @return Post
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user
     * @return Post
     */
    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }
    /**
     * @return Collection
     */
    public function getLikedBy()
    {
        return $this->likedBy;
    }
    public function like(User $user)
    {
        if(!$this->likedBy->contains($user)) {
            $this->likedBy->add($user);
        }
    }
    public function unLike(User $user)
    {
        if($this->likedBy->contains($user)) {
            $this->likedBy->removeElement($user);
        }
    }

}