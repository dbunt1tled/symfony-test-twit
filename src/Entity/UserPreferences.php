<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPreferencesRepository")
 */
class UserPreferences
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $locale;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLocale():string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return UserPreferences
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }
}
