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
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\CategoryRepository")
 * @Gedmo\Tree(type="materializedPath", activateLocking=true)
 * @MongoDB\HasLifecycleCallbacks
 */
class Category
{
    use TimestampableTrait, SluggableTrait, EnabledFieldTrait;

    /**
     * @MongoDB\Id
     * @Gedmo\TreePathSource
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * //Gedmo\TreePathSource
     * @Assert\NotBlank()
     * @Assert\Length(min="10", minMessage="minimum 10 symbols")
     */
    private $title;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min="10", minMessage="minimum 10 symbols")
     */
    private $description;

    /** @MongoDB\ReferenceMany(targetDocument="Post", mappedBy="category") */
    private $posts;

    /**
     * @Gedmo\TreePath(separator="|")
     * @MongoDB\Field(type="string")
     * @MongoDB\UniqueIndex(order="asc")
     */
    private $path;

    /**
     * @Gedmo\TreeLevel
     * @MongoDB\Field(type="int")
     */
    private $level;

    /**
     * @Gedmo\TreeParent
     * @MongoDB\ReferenceOne(targetDocument="Category")
     */
    private $parent;

    /**
     * @Gedmo\TreeLockTime
     * @MongoDB\Field(type="date")
     */
    private $lockTime;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return Category
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
     * @param Post $post
     * @return Category
     */
    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Post $post
     * @return Category
     */
    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts()
    {
        $this->posts = $this->posts ?: new ArrayCollection();
        return $this->posts;
    }

    /**
     * @param $posts
     * @return Category
     */
    public function setPosts($posts): self
    {
        $this->posts = $posts;
        return $this;
    }

    /**
     * @param Category|null $parent
     * @return Category
     */
    public function setParent(Category $parent = null): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getLockTime()
    {
        return $this->lockTime;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return Category
     */
    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

}