<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17.10.18
 * Time: 11:04
 */

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\EmbeddedDocument
 */
class UserPreferences
{
    /**
     * @MongoDB\Field(type="string")
     */
    protected $locale;

    public function __construct()
    {
        $this->locale = 'en';
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }
    public function toArray() {
        return get_object_vars($this);
        // alternatively, you could do:
        // return ['username' => $this->username, 'password' => '****']
    }
}