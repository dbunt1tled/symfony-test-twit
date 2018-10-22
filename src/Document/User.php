<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 15.10.18
 * Time: 22:21
 */

namespace App\Document;


use App\Document\Traits\EnabledFieldTrait;
use App\Document\Traits\TimestampableTrait;
use App\Hydrator\Hydro;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use MongoDB\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\UserRepository")
 * @MongoDB\HasLifecycleCallbacks
 * @MongoDBUnique(fields="email", message="This e-mail is already used")
 */
class User implements UserInterface, \Serializable
{
    use TimestampableTrait,EnabledFieldTrait;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $firstName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $lastName;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank(
     *     message="Email не может быть пустым"
     * )
     * @Assert\Email(
     *     message="Email имеет не верный формат"
     * )
     */
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $password;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Assert\NotBlank(
     *     message="Не может быть пустым"
     * )
     * @Assert\Length(
     *     min=6,
     *     max=4096,
     *     minMessage="Минимум шесть символов"
     * )
     */
    protected $plainPassword;


    /**
     * @MongoDB\Field(type="string", nullable=true)
     */
    protected $confirmationToken;

    /**
     * @MongoDB\Field(name="roles", type="collection")
     */
    private $roles;

    /**
     * @MongoDB\EmbedOne(targetDocument="UserPreferences")
     **/
    private $preferences;

    /** @MongoDB\ReferenceMany(targetDocument="Post", mappedBy="user") */
    private $posts;

    public function __construct()
    {
        $this->roles = [self::ROLE_USER];
        $this->preferences = new UserPreferences();
        $this->posts = new ArrayCollection();
        $this->enabled = false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @return null|string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken(string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    /*
     * For UserChecker
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }
    public function isAccountNonExpired()
    {
        return true;
    }
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPlainPassword(string $password): self
    {
        $this->plainPassword = $password;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @param $roles
     * @return User
     */
    public function setRoles($roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        $role = $this->roles;
        if (empty($role)) {
            $role[] = self::ROLE_USER;
        }
        return array_unique($role);
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->enabled,
        ]);
    }
    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->enabled) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return ($this->firstName . " " .$this->lastName);
    }
    /**
     * @return UserPreferences
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param UserPreferences $preferences
     * @return User
     */
    public function setPreferences(UserPreferences $preferences): self
    {
        $this->preferences = $preferences;
        return $this;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

}